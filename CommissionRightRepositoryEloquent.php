<?php

namespace App\Repositories\Commissions;

use App\Models\Assignment;
use App\Models\CommissionRight;
use App\Validators\CommissionRightValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class CommissionRightRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CommissionRightRepositoryEloquent extends BaseRepository implements CommissionRightRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CommissionRight::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
        return CommissionRightValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get all commission rights
     *
     * @param Assignment $assignment
     * @return mixed
     */
    public function getListAll(Assignment $assignment)
    {
        $statuses = $this->model
            ->whereCompanyId($assignment->story->company_id)
            ->orderBy('name')
            ->get();

        return $statuses;
    }
}
