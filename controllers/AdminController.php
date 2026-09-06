<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;
use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\sociocraticGovernance\models\{Circle, Configuration, PermanentMembership};
use humhub\modules\sociocraticGovernance\services\Access;

class AdminController extends \humhub\components\Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), ['verbs' => [
            'class' => \yii\filters\VerbFilter::class, 'actions' => ['remove' => ['POST']],
        ]]);
    }
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        $this->requireAuthority();
        return true;
    }
    private function requireAuthority(): void
    {
        $config = Configuration::findOne(1);
        if (\Yii::$app->user->isGuest || !$config ||
            ($config->authority_user_id ? (int) $config->authority_user_id !== (int) Yii::$app->user->id : !Yii::$app->user->isAdmin())) {
            throw new \yii\web\ForbiddenHttpException('Nur die konfigurierte Admin-Sonderrolle darf diese Angaben ändern.');
        }
    }
    public function actionIndex()
    {
        $config = Configuration::findOne(1);
        $permanent = new PermanentMembership();
        $post = Yii::$app->request->post();
        if ($config->load($post)) {
            $tx = Yii::$app->db->beginTransaction();
            try {
                $sql = 'SELECT [[id]] FROM {{%sg_config}} WHERE [[id]]=1';
                if (Yii::$app->db->driverName !== 'sqlite') { $sql .= ' FOR UPDATE'; }
                Yii::$app->db->createCommand($sql)->queryScalar();
                $this->requireAuthority();
                if ($config->validate()) {
                    $root = $config->root_space_id ? Circle::findOne($config->root_space_id) : null;
                    if ($config->root_space_id && (!$root || $root->parent_space_id || !Access::enabled($root->space))) {
                        $config->addError('root_space_id', 'Bitte einen eingerichteten Kreis ohne Oberkreis wählen.');
                    }
                    $authority = $config->authority_user_id ? User::findOne($config->authority_user_id) : null;
                    if ($authority && (int) $authority->status !== User::STATUS_ENABLED) {
                        $config->addError('authority_user_id', 'Bitte eine aktive Person wählen.');
                    }
                    if (!$config->hasErrors() && $config->save(false)) {
                        $tx->commit();
                        Yii::$app->session->setFlash('success', 'Backend-Einstellungen gespeichert.');
                        return $this->redirect(['/sociocratic-governance/directory/index']);
                    }
                }
                $tx->rollBack();
            } catch (\Throwable $e) { $tx->rollBack(); throw $e; }
        } elseif ($permanent->load($post) && $permanent->validate()) {
            $space = Space::findOne($permanent->space_id);
            $user = User::findOne($permanent->user_id);
            if (!$space || !Access::enabled($space) || !$user || !$space->isMember($user->id) || (int) $user->status !== User::STATUS_ENABLED) {
                $permanent->addError('user_id', 'Bitte ein aktives Mitglied des gewählten Kreises wählen.');
            } elseif ($permanent->save()) {
                Yii::$app->session->setFlash('success', 'Dauerhafte Mitgliedschaft dokumentiert.');
                return $this->redirect(['index']);
            }
        }
        $spaces = [];
        foreach (Circle::find()->with('space')->all() as $circle) {
            if (Access::enabled($circle->space)) { $spaces[$circle->space_id] = $circle->space->name; }
        }
        $users = [];
        foreach (User::find()->where(['status' => User::STATUS_ENABLED])->all() as $user) { $users[$user->id] = $user->displayName; }
        return $this->render('index', ['config' => $config, 'permanent' => $permanent, 'spaces' => $spaces,
            'users' => $users, 'declarations' => PermanentMembership::find()->with(['space', 'user'])->all()]);
    }
    public function actionRemove($id)
    {
        $item = PermanentMembership::findOne((int) $id);
        if (!$item) { throw new \yii\web\NotFoundHttpException(); }
        $item->delete();
        return $this->redirect(['index']);
    }
}
