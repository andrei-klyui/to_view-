<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Office;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficePolicy
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
     * Determine whether the user can get list office.
     *
     * @param User $user
     * @return bool|mixed
     */
    public function list(User $user)
    {
        return $user->hasPermissionTo('list offices');
    }

    /**
     * Determine whether the user can create office.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create office');
    }

    /**
     * Determine whether the user can update the office.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Office  $office
     * @return mixed
     */
    public function update(User $user, Office $office)
    {
        return $user->hasPermissionTo('update office');
    }

    /**
     * Determine whether the user can delete the office.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Office  $office
     * @return mixed
     */
    public function delete(User $user, Office $office)
    {
        return $user->hasPermissionTo('delete office');
    }

    /**
     * Determine whether the user can remove the office cover.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Office  $office
     * @return mixed
     */
    public function removeCover(User $user, Office $office)
    {
        return $user->hasPermissionTo('remove office cover');
    }
}
