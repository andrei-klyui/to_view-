<?php

namespace App\Repositories;

use App\Models\Issue;
use App\Models\Post;
use App\Models\User;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface NotificationsRepository.
 *
 * @package namespace App\Repositories;
 */
interface NotificationsRepository extends RepositoryInterface
{
    /**
     * Set read notifications by issue.
     *
     * @param Issue $issue
     */
    public function setReadByIssue(Issue $issue);

    /**
     * Set read notifications by post.
     *
     * @param Post $post
     */
    public function setReadByPost(Post $post);

    /**
     * Set read notifications by new user.
     *
     * @param User $user
     */
    public function setReadByNewUser(User $user);

    /**
     * Set read notifications by user and issue id.
     *
     * @param User $user
     * @param int $issueId
     */
    public function setReadByUserAndIssue(User $user, int $issueId);

    /**
     * Set read notifications by user and post id.
     *
     * @param User $user
     * @param int $postId
     */
    public function setReadByUserAndPost(User $user, int $postId);

    /**
     * Set read notifications by user and new user.
     *
     * @param User $user
     * @param int $postId
     */
    public function setReadByUserAndNewUser(User $user, int $userId);
}
