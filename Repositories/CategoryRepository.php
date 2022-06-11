<?php

namespace App\Repositories;

use App\Models\Filters\CategoryFilter;
use Prettus\Repository\Contracts\RepositoryInterface;
use \Illuminate\Database\Eloquent\Model;
use App\Models\Category;

/**
 * Interface CategoryRepository.
 *
 * @package namespace App\Repositories;
 */
interface CategoryRepository extends RepositoryInterface
{

    /**
     * Get category by id without parent.
     *
     * @param int $id
     * @return Category|Model|object|null
     */
    public function getByIdWithoutParent(int $id);

    /**
     * Get list category and become search with pagination
     * The search goes through one field 'name'
     * that refine the search
     *
     * @param CategoryFilter $filters
     * @return mixed
     */
    public function getList(CategoryFilter $filters): object;
}
