<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;

use Yii;
use humhub\modules\sociocraticGovernance\search\GovernanceSearch;

class SearchController extends \humhub\components\Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (Yii::$app->user->isGuest) { throw new \yii\web\ForbiddenHttpException('Bitte anmelden.'); }
        return true;
    }

    public function actionIndex($keyword = null)
    {
        $keyword = is_string($keyword) ? trim($keyword) : '';
        if (mb_strlen($keyword) > 120) { throw new \yii\web\BadRequestHttpException('Die Suchanfrage ist zu lang.'); }
        return $this->render('index', (new GovernanceSearch())->search($keyword, 100) + ['keyword' => $keyword]);
    }
}
