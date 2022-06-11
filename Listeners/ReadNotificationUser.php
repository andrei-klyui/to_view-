<?php

namespace App\Listeners;

use App\Events\UserRead;
use App\Repositories\NotificationsRepository;
use Illuminate\Support\Facades\Auth;

class ReadNotificationUser
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
     * @param UserRead $event
     * @throws \Exception
     */
    public function handle(UserRead $event)
    {
        $this->notificationsRepository->setReadByUserAndNewUser(Auth::user(), $event->userId);
    }
}
