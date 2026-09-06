<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm};
use humhub\modules\sociocraticGovernance\services\{Access, CircleService};
use yii\web\{ForbiddenHttpException, NotFoundHttpException};

class CircleController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (!Access::read($this->contentContainer)) { throw new NotFoundHttpException(); }
        return true;
    }
    public function actionIndex()
    {
        $circle = Circle::findOne($this->contentContainer->id);
        return $this->render('index', [
            'space' => $this->contentContainer, 'circle' => $circle,
            'circles' => Access::visibleCircles(), 'canWrite' => Access::write($this->contentContainer),
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
    public function actionGuide() { return $this->render('guide', ['space' => $this->contentContainer]); }
}

