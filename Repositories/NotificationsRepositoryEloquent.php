<?php

namespace App\Repositories;

use App\Models\Issue;
use App\Models\Post;
use App\Models\User;
use App\Notifications\IssueCreated;
use App\Notifications\PostCreated;
use App\Notifications\UserCreated;
use Illuminate\Support\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Notifications;
use App\Validators\NotificationsValidator;

/**
 * Class NotificationsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationsRepositoryEloquent extends BaseRepository implements NotificationsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notifications::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Set read notifications by issue.
     *
     * @param Issue $issue
     */
    public function setReadByIssue(Issue $issue)
    {
        \DB::table('notifications')
            ->where('type', IssueCreated::class)
            ->where('data->issue_id', $issue->id)
            ->update([
                'read_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }

    /**
     * Set read notifications by post.
     *
     * @param Post $post
     */
    public function setReadByPost(Post $post)
    {
        \DB::table('notifications')
            ->where('type', PostCreated::class)
            ->where('data->post_id', $post->id)
            ->update([
                'read_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }

    /**
     * Set read notifications by user.
     *
     * @param User $user
     */
    public function setReadByNewUser(User $user)
    {
        \DB::table('notifications')
            ->where('type', UserCreated::class)
            ->where('data->user_id', $user->id)
            ->update([
                'read_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }

    /**
     * Set read notifications by user and issue id.
     *
     * @param User $user
     * @param int $issueId
     */
    public function setReadByUserAndIssue(User $user, int $issueId)
    {
        $user->notifications()
            ->where('type', IssueCreated::class)
            ->where('data->issue_id', $issueId)
            ->update([
                'read_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }

    /**
     * Set read notifications by user and post id.
     *
     * @param User $user
     * @param int $postId
     */
    public function setReadByUserAndPost(User $user, int $postId)
    {
        $user->notifications()
            ->where('type', PostCreated::class)
            ->where('data->post_id', $postId)
            ->update([
                'read_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }

    /**
     * Set read notifications by user and new user.
     *
     * @param User $user
     * @param int $userId
     */
    public function setReadByUserAndNewUser(User $user, int $userId)
    {
        $user->notifications()
            ->where('type', PostCreated::class)
            ->where('data->user_id', $userId)
            ->update([
                'read_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    }
}
