<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\Circle;
use humhub\modules\space\models\Membership;
use humhub\modules\content\models\Content;
use humhub\modules\user\models\User;

/** Personal links; permission checks precede both labels and unread indicators. */
final class PersonalNavigation
{
    public function data(User $user): array
    {
        $chatGroups = [];
        if (Yii::$app->hasModule('conversations') && class_exists(\humhub\modules\conversations\services\ConversationOverviewService::class)) {
            $chatGroups = (new \humhub\modules\conversations\services\ConversationOverviewService())->groupedBySpace($user);
        }
        $navigationSpaces = [];
        $navigationProjectCircles = [];
        $navigationCompetenceCircles = [];
        $memberships = Membership::findByUser($user)->with('space')->all();
        $spaceIds = array_map(static fn($membership): int => (int) $membership->space_id, $memberships);
        $circles = $spaceIds ? Circle::find()->where(['space_id' => $spaceIds])->indexBy('space_id')->all() : [];
        foreach ($memberships as $membership) {
            $space = $membership->space;
            if (!$space || !$space->isMember($user->id)) { continue; }
            $circle = $circles[(int) $space->id] ?? null;
            if ($circle && !Access::readForUser($space, $user)) { continue; }
            $unread = false;
            $query = Content::find()->where([
                'contentcontainer_id' => $space->contentcontainer_id,
                'stream_channel' => 'default', 'archived' => 0,
            ])->andWhere(['<>', 'created_by', $user->id]);
            if ($membership->last_visit !== null) {
                $query->andWhere(['>', 'created_at', $membership->last_visit]);
            }
            foreach ($query->each() as $content) {
                if ($content->canView($user)) { $unread = true; break; }
            }
            $item = ['space' => $space, 'unread' => $unread];
            if (!$circle) { $navigationSpaces[] = $item; }
            elseif ($circle->isCompetenceCircle()) { $navigationCompetenceCircles[] = $item; }
            else { $navigationProjectCircles[] = $item; }
        }
        $byName = static fn(array $a, array $b): int => strnatcasecmp($a['space']->name, $b['space']->name);
        usort($navigationSpaces, $byName);
        usort($navigationProjectCircles, $byName);
        usort($navigationCompetenceCircles, $byName);
        return compact('chatGroups', 'navigationSpaces', 'navigationProjectCircles', 'navigationCompetenceCircles');
    }
}
