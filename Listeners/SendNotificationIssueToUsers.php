<?php

namespace App\Listeners;

use App\Events\IssueCreated;
use App\Models\Issue;
use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;

/**
 * Class SendNotificationIssueToUsers
 * @package App\Listeners
 */
class SendNotificationIssueToUsers
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * SendNotificationIssueToUsers constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  IssueCreated  $event
     * @return void
     */
    public function handle(IssueCreated $event)
    {
        /** @var Issue $issue */
        $issue = $event->issue;

        $users = $this->userRepository->getUsersAdminByIssue($issue->id);

        Notification::send($users, new \App\Notifications\IssueCreated($issue));
    }
}
