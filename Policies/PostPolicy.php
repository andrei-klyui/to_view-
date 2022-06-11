<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can get list posts.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list posts');
    }

    /**
     * Determine whether the user can create post.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create post');
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        return $user->hasPermissionTo('update post');
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        return $user->hasPermissionTo('delete post');
    }

    /**
     * Determine whether the user can remove the post cover.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function removeCover(User $user, Post $post)
    {
        return $user->hasPermissionTo('remove post cover');
    }
}
