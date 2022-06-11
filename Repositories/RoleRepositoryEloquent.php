<?php

namespace App\Repositories;

use App\Models\Filters\RoleFilter;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Role;
use App\Validators\RoleValidator;
use Prettus\Validator\Contracts\ValidatorInterface;

/**
 * Class RolesRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RoleRepositoryEloquent extends BaseRepository implements RoleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return RoleValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));

    }

    /**
     * Get list roles. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @param int|null $lengthDescription
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getList(RoleFilter $filters)
    {
        $roles = Role::filter($filters)
            ->select(['roles.id', 'roles.name', 'roles.title', 'roles.created_at', 'roles.updated_at'])
            ->orderBy('roles.created_at', 'desc')
            ->paginate(config('view.roles'));

        return $roles;
    }

    /**
     * @param $id
     * @return Role
     */
    public function getRoleById($id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * @param array $attributes
     * @return Model
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes): Model
    {
        $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_CREATE);

        return Role::create($attributes);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return Role
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateById(array $attributes, $id): Role
    {
        $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_UPDATE);

        $role = Role::find($id);
        $role->update($attributes);

        return $role;
    }

    /**
     * @param $id
     * @return Role
     */
    public function getByIdWithRelations($id): Role
    {
        return Role::with('permissions')->find($id);
    }
}
