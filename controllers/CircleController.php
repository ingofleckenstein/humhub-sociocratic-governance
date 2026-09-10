<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, PermanentMembership};
use humhub\modules\sociocraticGovernance\services\{Access, CircleService};
use yii\web\{ForbiddenHttpException, NotFoundHttpException};

class CircleController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];
    public function behaviors()
    {
        return array_merge(parent::behaviors(), ['verbs' => [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => ['publish' => ['POST']],
        ]]);
    }
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (!Access::read($this->contentContainer)) { throw new NotFoundHttpException(); }
        return true;
    }
    public function actionIndex($section = 'overview')
    {
        $circle = Circle::findOne($this->contentContainer->id);
        // A published competence circle opens the standard Space home. Before
        // publication its admins need the circle view to finish the profile
        // and use the publication action.
        if ($circle && $circle->isCompetenceCircle() && $circle->is_published) {
            return $this->redirect($this->contentContainer->createUrl('/space/space/home'));
        }
        return $this->render('index', [
            'space' => $this->contentContainer, 'circle' => $circle,
            'circles' => Access::visibleCircles(), 'canWrite' => Access::write($this->contentContainer),
            'canPublish' => Access::admin($this->contentContainer) && $circle && !$circle->is_published,
            'permanentMemberships' => PermanentMembership::find()->where(['space_id' => $this->contentContainer->id])->with('user')->all(),
            'section' => $this->section($section),
        ]);
    }
    public function actionEdit()
    {
        $space = $this->contentContainer;
        if (!Access::write($space)) { throw new ForbiddenHttpException('Nur Kreismitglieder dürfen schreiben.'); }
        $form = CircleForm::forCircle(Circle::findOne($space->id));
        if ($form->load(Yii::$app->request->post()) && (new CircleService())->save($space, $form)) {
            Yii::$app->session->setFlash('success', 'Kreisprofil und Rollen gespeichert.');
            return $this->redirect($space->createUrl('/sociocratic-governance/circle/index'));
        }
        $parents = [];
        foreach (Access::visibleCircles() as $circle) {
            if ((int) $circle->space_id !== (int) $space->id) { $parents[$circle->space_id] = $circle->space->name; }
        }
        return $this->render('edit', ['space' => $space, 'form' => $form, 'parents' => $parents, 'members' => Access::memberOptions($space)]);
    }
    public function actionPublish()
    {
        try {
            (new CircleService())->publish($this->contentContainer);
            Yii::$app->session->setFlash('success', 'Der Space ist veröffentlicht. Die Willkommensnachricht wurde im Stream angelegt.');
        } catch (\DomainException $e) {
            $message = 'Veröffentlichung fehlgeschlagen: ' . $e->getMessage();
            Yii::$app->session->setFlash('error', $message);
            Yii::$app->session->setFlash('sgPublicationError', $message);
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);
            $message = 'Veröffentlichung fehlgeschlagen. Bitte versuche es erneut oder wende dich an die Administration.';
            Yii::$app->session->setFlash('error', $message);
            Yii::$app->session->setFlash('sgPublicationError', $message);
        }
        return $this->redirect($this->contentContainer->createUrl('/sociocratic-governance/circle/index'));
    }
    public function actionGuide() { return $this->render('guide', ['space' => $this->contentContainer]); }
    private function section($section): string
    {
        return is_string($section) && in_array($section, ['overview', 'mandate', 'roles', 'connections'], true)
            ? $section : 'overview';
    }
}
