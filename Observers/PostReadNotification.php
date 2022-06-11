<?php

namespace App\Observers;

use App\Models\Post;
use App\Repositories\NotificationsRepository;

class PostReadNotification
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
     * Observer to deleted post.
     *
     * @param Post $post
     */
    public function deleted(Post $post)
    {
        $this->notificationsRepository->setReadByPost($post);
    }
}
