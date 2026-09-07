<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\space\models\Space;

/** Selects only directly involved people; delivery performs an access check again. */
final class WorkNotificationRecipients
{
    public static function ids(WorkItem $item, string $action, array $before = []): array
    {
        $currentReviewers = WorkAccess::reviewers($item->space);
        $previousReviewers = [];
        if ($action === 'delegate' && isset($before['space_id'])) {
            $previous = Space::findOne((int) $before['space_id']);
            $previousReviewers = $previous ? WorkAccess::reviewers($previous) : [];
        }
        $authorAndAssignee = [(int) $item->author_id, (int) $item->assignee_id];
        if ($action === 'delegate' && !empty($before['assignee_id'])) {
            $authorAndAssignee[] = (int) $before['assignee_id'];
        }
        $authorAndAssignee = array_filter($authorAndAssignee);
        $recipients = match ($action) {
            'created_idea', 'created_task' => $currentReviewers,
            'accepted' => $authorAndAssignee,
            'commented', 'edited' => array_merge($authorAndAssignee, $currentReviewers),
            'claimed', 'started', 'submitted' => array_merge($authorAndAssignee, $currentReviewers),
            'approved', 'returned', 'rejected' => $authorAndAssignee,
            'delegated' => array_merge($authorAndAssignee, $currentReviewers, $previousReviewers),
            default => $authorAndAssignee,
        };
        return array_values(array_unique(array_map('intval', $recipients)));
    }
}
