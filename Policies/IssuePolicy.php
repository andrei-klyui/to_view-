<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Issue;
use Illuminate\Auth\Access\HandlesAuthorization;

class IssuePolicy
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
     * Determine whether the user can get list issue.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list issues');
    }

    /**
     * Determine whether the user can create issue.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create issue');
    }

    /**
     * Determine whether the user can update issue.
     *
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    public function update(User $user, Issue $issue)
    {
        return $user->hasPermissionTo('update issue');
    }

    /**
     * Determine whether the user can delete issue.
     *
     * @param User $user
     * @param Issue $issue
     * @return bool
     */
    public function delete(User $user, Issue $issue)
    {
        return $user->hasPermissionTo('delete issue');
    }
}
