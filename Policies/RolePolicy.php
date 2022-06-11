<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public const SUPER_ADMIN = 'super-admin';
    public const LIST_ROLES = 'list roles';
    public const CREATE_ROLE = 'create role';
    public const UPDATE_ROLE = 'update role';
    public const DELETE_ROLE = 'delete role';

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
     * Determine whether the user can get list roles.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo(self::LIST_ROLES);
    }

    /**
     * Determine whether the user can create role.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo(self::CREATE_ROLE);
    }

    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function update(User $user, Role $role)
    {
        return $user->hasPermissionTo(self::UPDATE_ROLE);
    }

    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function delete(User $user, Role $role)
    {
        return $user->hasPermissionTo(self::DELETE_ROLE);
    }
}
