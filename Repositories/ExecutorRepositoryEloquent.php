<?php

namespace App\Repositories;

use App\Models\Filters\ExecutorFilter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Executor;
use App\Validators\ExecutorValidator;

/**
 * Class ExecutorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ExecutorRepositoryEloquent extends BaseRepository implements ExecutorRepository
{
    # todo: needs to change this, what if id = 1 doesn't exist
    const DEFAULT_EXECUTOR_ID = 1;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Executor::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return ExecutorValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get Executor id by default.
     *
     * @return int
     */
    public function getDefaultExecutorId(): int
    {
        if ($officeManagerExecutor = Executor::get()->where('description', 'office-manager')->first()) {

            return $officeManagerExecutor->id;
        }

        return self::DEFAULT_EXECUTOR_ID;
    }

    /**
     * Get list executors and become search with pagination
     * The search goes through two fields 'name', 'email'
     * that refine the search
     *
     * @param ExecutorFilter $filters
     * @return mixed
     */
    public function getList(ExecutorFilter $filters)
    {
        return Executor::filter($filters)
            ->orderBy('executors.name', 'ASC')
            ->paginate(config('view.count.executors'));
    }
}
