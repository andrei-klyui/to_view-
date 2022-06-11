<?php

namespace App\Observers;

use App\Models\Issue;
use App\Repositories\NotificationsRepository;

class IssueReadNotification
{
    /**
     * @var NotificationsRepository
     */
    private $notificationsRepository;

    /**
     * IssueRemoveNotification constructor.
     * @param NotificationsRepository $notificationsRepository
     */
    public function __construct(
        NotificationsRepository $notificationsRepository
    ) {
        $this->notificationsRepository = $notificationsRepository;
    }

    /**
     * Observer to deleted issue.
     *
     * @param Issue $issue
     */
    public function deleted(Issue $issue)
    {
        $this->notificationsRepository->setReadByIssue($issue);
    }
}
