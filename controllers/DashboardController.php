<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;

use Yii;
use humhub\modules\sociocraticGovernance\models\WorkResource;
use humhub\modules\sociocraticGovernance\services\ParticipationDashboard;

class DashboardController extends \humhub\components\Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (Yii::$app->user->isGuest) { throw new \yii\web\ForbiddenHttpException('Bitte anmelden.'); }
        return true;
    }

    public function actionIndex($focus = 'topics', $topic = null, $resourceType = null)
    {
        if (!in_array($focus, ['topics', 'resources'], true)) { $focus = 'topics'; }
        if ($topic !== null && (!is_string($topic) || mb_strlen($topic) > 80)) { throw new \yii\web\BadRequestHttpException('Ungültiges Thema.'); }
        if ($resourceType !== null && (!is_string($resourceType) || !array_key_exists($resourceType, WorkResource::TYPES))) {
            throw new \yii\web\BadRequestHttpException('Ungültiger Ressourcenfilter.');
        }
        return $this->render('index', (new ParticipationDashboard())->data($topic, $resourceType) + ['focus' => $focus]);
    }
}
