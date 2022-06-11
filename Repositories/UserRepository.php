<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\UploadedFile;
use App\Models\Filters\UserFilter;
use App\Models\User;

/**
 * Interface UserRepository.
 *
 * @package namespace App\Repositories;
 */
interface UserRepository extends RepositoryInterface
{
    /**
     * Get list users with role administrator by issue id
     *
     * @param int $issueId
     * @return array
     */
    public function getUsersAdminByIssue(int $issueId);

    /**
     * Move file avatar to upload folder
     *
     * @param \Illuminate\Http\UploadedFile|null $avatarFile
     * @return string|null
     */
    public function moveAvatarToFolder(UploadedFile $avatarFile = null);

    /**
     * Remove old avatar
     *
     * @param $userAvatar
     */
    public function removeAvatar($userAvatar = null);

    /**
     * Remove old user avatar
     *
     * @param \App\Models\User $user
     */
    public function removeUserAvatar(User $user);

    /**
     * Update user with avatar but without validation
     *
     * @param array $attributes
     * @return \Illuminate\Contracts\Auth\Authenticatable|mixed|null
     * @throws \Exception
     */
    public function updateWithAvatar(array $attributes);

    /**
     * Update user by user and attributes
     *
     * @param User $user
     * @param array $attributes
     * @return User
     * @throws \Exception
     */
    public function updateByUser(User $user, array $attributes);

    /**
     * Get list users and become search with pagination
     * The search goes through three fields 'name', 'email', 'role'
     * that refine the search
     *
     * @param UserFilter $filters
     * @return mixed
     */
    public function getList(UserFilter $filters): object;

    /**
     * Create new user
     *
     * @param array $attributes
     *
     * @return User|\Illuminate\Database\Eloquent\Model|mixed
     * @throws \Exception
     */
    public function createWithAvatar(array $attributes);

    /**
     * @param array $roles
     * @return object
     */
    public function getUsersByRoles(array $roles): object;

    /**
     * @param string $role
     * @return User
     */
    public function getUserByRole(string $role): \App\Models\User;

    /**
     * @param User $modelUser
     * @return object
     */
    public function getUserRole(User $modelUser): object;
}
