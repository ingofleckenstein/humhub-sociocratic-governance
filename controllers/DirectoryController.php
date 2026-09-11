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
    public function actionIndex($view = 'table', $query = null, $type = 'all', $mine = null)
    {
        $directory = (new CircleDirectory())->data();
        if ($query !== null && (!is_string($query) || mb_strlen($query) > 100)) {
            throw new \yii\web\BadRequestHttpException('Ungültige Suche.');
        }
        $query = trim((string) $query);
        if (!in_array($type, ['all', 'project', 'competence'], true)) {
            throw new \yii\web\BadRequestHttpException('Ungültiger Kreistyp.');
        }
        if ($mine !== null && (string) $mine !== '1') {
            throw new \yii\web\BadRequestHttpException('Ungültiger Mitgliedschaftsfilter.');
        }
        $mine = (string) $mine === '1';
        $filtersActive = $query !== '' || $type !== 'all' || $mine;
        if ($filtersActive) {
            $userId = (int) Yii::$app->user->id;
            $matchingIds = [];
            foreach ($directory['rows'] as $row) {
                $circle = $row['circle'];
                if ($type !== 'all' && ($circle->isCompetenceCircle() ? 'competence' : 'project') !== $type) { continue; }
                if ($mine && !$circle->space->isMember($userId)) { continue; }
                $haystack = $circle->space->name . "\n" . $circle->mandateSummary() . "\n" . $circle->purpose;
                if ($query !== '' && mb_stripos($haystack, $query) === false) { continue; }
                $matchingIds[(int) $circle->space_id] = true;
            }
            $directory['rows'] = array_values(array_filter($directory['rows'], static fn(array $row): bool => isset($matchingIds[(int) $row['circle']->space_id])));
            foreach ($directory['rows'] as &$row) { $row['depth'] = 0; }
            unset($row);
            $directory['projectRows'] = array_values(array_filter($directory['rows'], static fn(array $row): bool => !$row['circle']->isCompetenceCircle()));
            $directory['competenceRows'] = array_values(array_filter($directory['rows'], static fn(array $row): bool => $row['circle']->isCompetenceCircle()));
            $directory['nodes'] = array_filter($directory['nodes'], static fn($id): bool => isset($matchingIds[(int) $id]), ARRAY_FILTER_USE_KEY);
            $directory['focusSpaceIds'] = array_values(array_filter($directory['focusSpaceIds'], static fn($id): bool => isset($matchingIds[(int) $id])));
        }
        return $this->render('index', $directory + [
            'circles' => Access::visibleCircles(), 'activeView' => $view === 'map' ? 'map' : 'table',
            'query' => $query, 'selectedType' => $type, 'onlyMine' => $mine,
        ]);
    }
}
