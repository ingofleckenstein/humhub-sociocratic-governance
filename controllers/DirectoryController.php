<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;
use Yii;
use humhub\modules\sociocraticGovernance\services\Access;
use humhub\modules\sociocraticGovernance\services\CircleDirectory;
class DirectoryController extends \humhub\components\Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (Yii::$app->user->isGuest) { throw new \yii\web\ForbiddenHttpException('Bitte anmelden.'); }
        return true;
    }
    public function actionIndex()
    {
        $directory = (new CircleDirectory())->data();
        return $this->render('index', $directory + ['circles' => Access::visibleCircles()]);
    }
}
