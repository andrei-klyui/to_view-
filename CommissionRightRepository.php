<?php

namespace App\Repositories\Commissions;

use App\Models\Assignment;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CommissionRightRepository.
 *
 * @package namespace App\Repositories;
 */
interface CommissionRightRepository extends RepositoryInterface
{
    /**
     * Get all commission rights
     *
     * @param Assignment $assignment
     * @return mixed
     */
    public function getListAll(Assignment $assignment);
}
