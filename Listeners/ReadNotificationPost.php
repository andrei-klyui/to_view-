<?php

namespace App\Listeners;

use App\Events\PostRead;
use App\Repositories\NotificationsRepository;
use Illuminate\Support\Facades\Auth;

class ReadNotificationPost
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
     * @param PostRead $event
     * @throws \Exception
     */
    public function handle(PostRead $event)
    {
        $user = Auth::user();

        $postId = $event->postId;

        $this->notificationsRepository->setReadByUserAndPost($user, $postId);
    }
}
