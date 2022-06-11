<?php

namespace App\Repositories;

use App\Models\Filters\UserFilter;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\User;
use App\Validators\UserValidator;
use Illuminate\Http\UploadedFile;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return UserValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get list users with role administrator by issue id
     *
     * @param int $issueId
     * @return array
     */
    public function getUsersAdminByIssue(int $issueId)
    {
        $query = User::fromRaw('issues, users')
            ->join('offices', 'users.office_id', '=', 'offices.id')
            ->join('categories', 'offices.id', '=', 'categories.office_id')
            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->join('roles', 'roles.id', '=', 'users_roles.role_id')
            ->select('users.*')
            ->whereColumn('categories.id', '=', 'issues.category_id')
            ->where('roles.name', '=', 'super-admin')
            ->where('issues.id', '=', $issueId)
            ->groupBy('users.id');

        return $query->get()->all();
    }

    /**
     * Move file avatar to upload folder
     *
     * @param \Illuminate\Http\UploadedFile|null $avatarFile
     * @return string|null
     */
    public function moveAvatarToFolder(UploadedFile $avatarFile = null)
    {
        $avatarPath = null;

        if ($avatarFile) {
            $avatarPath = \Storage::disk(env('FILESYSTEM_DRIVER'))->putFile('public/uploads/avatars', $avatarFile);
        }

        return $avatarPath;
    }

    /**
     * Remove old avatar
     *
     * @param $userAvatar
     */
    public function removeAvatar($userAvatar = null)
    {
        if ($userAvatar) {
            \Storage::delete($userAvatar);
        }
    }

    /**
     * Remove old user avatar
     *
     * @param \App\Models\User $user
     */
    public function removeUserAvatar(User $user)
    {
        $this->removeAvatar($user->avatar);
        $user->update(['avatar' => null, 'avatar_url' => null]);
    }

    /**
     * Update user with avatar but without validation
     *
     * @param array $attributes
     * @return \Illuminate\Contracts\Auth\Authenticatable|mixed|null
     * @throws \Exception
     */
    public function updateWithAvatar(array $attributes)
    {
        /** @var User */
        $user = \Auth::user();

        return $this->updateByUser($user, $attributes);
    }

    /**
     * Update user by user and attributes
     *
     * @param User $user
     * @param array $attributes
     * @return User
     * @throws \Exception
     */
    public function updateByUser(User $user, array $attributes)
    {
        $oldAvatar = $user->avatar;
        $updateAvatar = $this->updateAvatar($attributes);

        if ($updateAvatar === true) {
            $this->removeAvatar($oldAvatar);
            $user->update(['avatar' => $attributes['avatar'], 'avatar_url' => $attributes['avatar_url']]);
        }

        try {
            $user->with('roles')->get();
            if (isset($attributes['role_id'])) {
                $user->roles()->sync($attributes['role_id']);
            }
            if (isset($attributes['office_id'])) {
                $user->update(array('office_id' => $attributes['office_id']));
            }
        } catch (\Exception $e) {
            $this->removeAvatar($attributes['avatar'] ?? null);

            throw $e;
        }

        return $user->load('roles:id,name');
    }

    /**
     * Create new user
     *
     * @param array $attributes
     *
     * @return User|\Illuminate\Database\Eloquent\Model|mixed
     * @throws \Exception
     */
    public function createWithAvatar(array $attributes)
    {
        $this->updateAvatar($attributes);

        try {
            $user = User::create($attributes);
            $user->roles()->attach($attributes['role_id']);
        } catch (\Exception $e) {
            $this->removeAvatar($attributes['avatar']);

            throw $e;
        }

        return $user;
    }

    /**
     * Update avatar
     *
     * @param array $attributes
     */
    protected function updateAvatar(array &$attributes)
    {
        if (isset($attributes['avatar'])) {
            $avatar = $attributes['avatar'];
            $attributes['avatar'] = $this->moveAvatarToFolder($avatar);
            $attributes['avatar_url'] = \Storage::url($attributes['avatar']);

            return true;
        }

        return false;
    }

    /**
     * Get list users and become search with pagination
     * The search goes through three fields 'name', 'email', 'role'
     * that refine the search
     *
     * @param UserFilter $filters
     * @return mixed
     */
    public function getList(UserFilter $filters): object
    {
        return User::filter($filters)
            ->with('roles:id,name,title')
            ->select(['users.id', 'users.email', 'users.name', 'users.created_at', 'users.updated_at'])
            ->orderBy('users.name', 'ASC')
            ->paginate(config('view.count.users'));
    }

    /**
     * @param array $roles
     * @return object
     */
    public function getUsersByRoles(array $roles): object
    {
        return User::filter($this->createFilters(['roles' => $roles]))
            ->orderBy('users.name', 'ASC')
            ->paginate(config(config('view.count.users')));
    }

    /**
     * @param string $role
     * @return User
     */
    public function getUserByRole(string $role): \App\Models\User
    {
        return User::filter($this->createFilters(['roles' => [$role]]))->first();
    }

    /**
     * @param User $modelUser
     * @return object
     */
    public function getUserRole(User $modelUser): object
    {
        return $modelUser->roles()->first();
    }

    /**
     * Creates UserFilters
     *
     * @param array $filters
     * @return UserFilter
     */
    protected function createFilters(array $filters): UserFilter
    {
        $myRequest = new \Illuminate\Http\Request();
        $myRequest->setMethod('POST');

        foreach ($filters as $filter => $value) {
            $myRequest->request->add([$filter => $value]);
        }

        return new UserFilter($myRequest);
    }

}
