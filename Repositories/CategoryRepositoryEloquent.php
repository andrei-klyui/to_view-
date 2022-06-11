<?php

namespace App\Repositories;

use App\Models\Filters\CategoryFilter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Category;
use App\Validators\CategoryValidator;
use \Illuminate\Database\Eloquent\Model;

/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Category::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return CategoryValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get category by id without parent.
     *
     * @param int $id
     * @return Category|Model|object|null
     */
    public function getByIdWithoutParent(int $id)
    {
        $category = Category::where('parent', '=', null)
                            ->where('id', '=', $id);

        return $category->first();
    }

    /**
     * Get list category and become search with pagination
     * The search goes through one field 'name'
     * that refine the search
     *
     * @param CategoryFilter $filters
     * @return object
     */
    public function getList(CategoryFilter $filters): object
    {
        return Category::filter($filters)
            ->with('office:id,name')
            ->orderBy('categories.name', 'ASC')
            ->paginate(config('view.count.offices'));
    }
}
