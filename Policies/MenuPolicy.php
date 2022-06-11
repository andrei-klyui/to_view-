<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Menu;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
{
    use HandlesAuthorization;

    public const LIST_MENU = 'list menu';
    public const CREATE_MENU = 'create menu';
    public const UPDATE_MENU = 'update menu';
    public const DELETE_MENU = 'delete menu';

    /**
     * If user role is super-admin then all allows
     *
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can get list menu.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo(MenuPolicy::LIST_MENU);
    }

    /**
     * Determine whether the user can create menu.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo(MenuPolicy::CREATE_MENU);
    }

    /**
     * Determine whether the user can update the menu.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Menu  $menu
     * @return mixed
     */
    public function update(User $user, Menu $menu)
    {
        return $user->hasPermissionTo(MenuPolicy::UPDATE_MENU);
    }

    /**
     * Determine whether the user can delete the menu.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Menu  $menu
     * @return mixed
     */
    public function delete(User $user, Menu $menu)
    {
        return $user->hasPermissionTo(MenuPolicy::DELETE_MENU);
    }
}
