<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy
 * @package App\Policies
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * If user role is super-admin then all allows
     *
     * @param User $user
     * @return bool
     */
    public function before(User $user): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can get list users.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list users');
    }

    /**
     * Determine whether the user can create the user.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create user');
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $user
     * @param User $userEdit
     * @return bool|mixed
     */
    public function update(User $user, User $userEdit)
    {
        return $user->hasPermissionTo('update user');
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param User $user
     * @param User $userEdit
     * @return bool|mixed
     */
    public function delete(User $user, User $userEdit)
    {
        return $user->hasPermissionTo('delete user');
    }

    /**
     * Determine whether the user can remove the user avatar.
     *
     * @param User $user
     * @param User $userEdit
     * @return bool|mixed
     */
    public function removeUserAvatar(User $user, User $userEdit)
    {
        return $user->hasPermissionTo('remove user avatar');
    }
}
