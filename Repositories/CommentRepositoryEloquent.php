<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Comment;
use App\Validators\CommentValidator;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CommentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CommentRepositoryEloquent extends BaseRepository implements CommentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Comment::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return CommentValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Save a new entity in repository
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        $attributes['user_id'] = \Auth::id();

        return parent::create($attributes);
    }

    /**
     * Get list comments by commentableType and commentableId
     *
     * @param $commentableType
     * @param int $commentableId
     * @param int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByEntityId($commentableType, int $commentableId, int $limit = null)
    {
        $comments = Comment::with('user:id,name,avatar_url')
            ->whereCommentableType($commentableType)
            ->whereCommentableId($commentableId)
            ->orderByDesc('created_at')
            ->paginate($limit);

        return $comments;
    }

    /**
     * @param $id
     *
     * @return int
     */
    public function delete($id): int
    {
        return Comment::destroy($id);
    }
}
