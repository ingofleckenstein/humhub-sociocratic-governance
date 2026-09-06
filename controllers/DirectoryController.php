<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;
use Yii;
use humhub\modules\sociocraticGovernance\services\Access;
class DirectoryController extends \humhub\components\Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (Yii::$app->user->isGuest) { throw new \yii\web\ForbiddenHttpException('Bitte anmelden.'); }
        return true;
    }
    public function actionIndex() { return $this->render('index', ['circles' => Access::visibleCircles()]); }
}
