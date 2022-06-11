<?php

namespace App\Repositories;

use App\Models\Filters\OfficeFilter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Office;
use App\Validators\OfficeValidator;

/**
 * Class OfficeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OfficeRepositoryEloquent extends BaseRepository implements OfficeRepository
{
    /**
     * Get office by id
     *
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function getById(int $id)
    {
        $office = Office::findOrFail($id);

        return $office;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Office::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
        return OfficeValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get list offices and become search with pagination
     * The search goes through one field 'name'
     * that refine the search
     *
     * @param OfficeFilter $filters
     * @return mixed
     */
    public function getList(OfficeFilter $filters): object
    {
        return Office::filter($filters)
            ->select(['offices.id', 'offices.name', 'offices.created_at', 'offices.updated_at'])
            ->orderBy('offices.name', 'ASC')
            ->paginate(config('view.count.offices'));
    }
}
