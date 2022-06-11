<?php

namespace App\Repositories;

use App\Models\Filters\ExecutorFilter;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ExecutorRepository.
 *
 * @package namespace App\Repositories;
 */
interface ExecutorRepository extends RepositoryInterface
{
    /**
     * Get Executor id by default.
     *
     * @return int
     */
    public function getDefaultExecutorId(): int;

    /**
     * Get list executors and become search with pagination
     * The search goes through two fields 'name', 'email'
     * that refine the search
     *
     * @param ExecutorFilter $filters
     * @return mixed
     */
    public function getList(ExecutorFilter $filters);
}
