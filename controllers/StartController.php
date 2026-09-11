<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;

use Yii;
use humhub\modules\sociocraticGovernance\services\PersonalDashboard;

/** The personal entry point; it keeps the stream available as an activity link. */
class StartController extends \humhub\components\Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (Yii::$app->user->isGuest) { throw new \yii\web\ForbiddenHttpException('Bitte anmelden.'); }
        return true;
    }

    public function actionIndex()
    {
        return $this->render('index', (new PersonalDashboard())->data());
    }
}
