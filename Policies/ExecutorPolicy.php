<?php

namespace App\Policies;

use App\Models\Executor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExecutorPolicy
{
    use HandlesAuthorization;

    /**
     * If user role is super-admin then all allows
     *
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability)
    {
        return $user->isAdministrator();
    }

    /**
     *  Determine whether the user can get list executors.
     *
     * @param User $user
     * @return bool
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list executors');
    }

    /**
     * Determine whether the user can create executors.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create executors');
    }

    /**
     * Determine whether the user can update the executors.
     *
     * @param User $user
     * @param Executor $executor
     * @return bool
     */
    public function update(User $user, Executor $executor)
    {
        return $user->hasPermissionTo('update executor');
    }

    /**
     * Determine whether the user can delete the executor.
     *
     * @param User $user
     * @param Executor $executor
     * @return bool
     */
    public function delete(User $user, Executor $executor)
    {
        return $user->hasPermissionTo('delete executor');
    }
}
