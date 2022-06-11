<?php

namespace App\Repositories;

use App\Models\Filters\RoleFilter;
use App\Models\Role;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface RolesRepository.
 *
 * @package namespace App\Repositories;
 */
interface RoleRepository extends RepositoryInterface
{
    /**
     * @param $id
     * @return Role
     */
    public function getRoleById($id) :Role;

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function updateById(array $attributes, $id);

    /**
     * @param $id
     * @return Role
     */
    public function getByIdWithRelations($id) :Role;

    /**
     * Get list posts. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @param int|null $lengthDescription
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getList(RoleFilter $filters);
}
