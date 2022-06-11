<?php

namespace App\Listeners;

use App\Events\IssueRead;
use App\Repositories\NotificationsRepository;
use Illuminate\Support\Facades\Auth;

class ReadNotificationIssue
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
     * Handle the event.
     *
     * @param IssueRead $event
     * @throws \Exception
     */
    public function handle(IssueRead $event)
    {
        $user = Auth::user();

        $issueId = $event->issueId;

        $this->notificationsRepository->setReadByUserAndIssue($user, $issueId);
    }
}
