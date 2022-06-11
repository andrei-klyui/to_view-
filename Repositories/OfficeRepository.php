<?php

namespace App\Repositories;

use App\Models\Filters\OfficeFilter;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface OfficeRepository.
 *
 * @package namespace App\Repositories;
 */
interface OfficeRepository extends RepositoryInterface
{
    /**
     * Get post in short view for notification by id
     *
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function getById(int $id);

    /**
     * Get list offices and become search with pagination
     * The search goes through one field 'name'
     * that refine the search
     *
     * @param OfficeFilter $filters
     * @return mixed
     */
    public function getList(OfficeFilter $filters): object;
}
