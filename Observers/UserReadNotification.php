<?php

namespace App\Observers;

use App\Models\User;
use App\Repositories\NotificationsRepository;

class UserReadNotification
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
     * @param User $user
     */
    public function deleted(User $user)
    {
        $this->notificationsRepository->setReadByNewUser($user);
    }
}
