<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentsPolicy
{
    use HandlesAuthorization;

    /**
     * If user role is super-admin then all allows
     *
     * @param User $user
     * @param $ability
     * @return bool|void
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can get list comments.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list comments');
    }

    /**
     * Determine whether the user can create comments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create comments');
    }

    /**
     * Determine whether the user can update the comment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return mixed
     */
    public function update(User $user, Comment $comment)
    {
        return $user->hasPermissionTo('update comment');
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        return $user->hasPermissionTo('delete comment');
    }
}
