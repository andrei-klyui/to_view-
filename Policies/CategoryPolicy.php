<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
     *  Determine whether the user can get list category.
     *
     * @param User $user
     * @return bool
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list categories');
    }

    /**
     * Determine whether the user can create category.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create category');
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function update(User $user, Category $category)
    {
        return $user->hasPermissionTo('update category');
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function delete(User $user, Category $category)
    {
        return $user->hasPermissionTo('delete category');
    }
}
