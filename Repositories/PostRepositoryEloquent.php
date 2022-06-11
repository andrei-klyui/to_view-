<?php

namespace App\Repositories;

use App\Models\Filters\PostFilter;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Post;
use App\Validators\PostValidator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PostRepositoryEloquent extends BaseRepository implements PostRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'   =>  'like',
        'title'         =>  'like'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Post::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return PostValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get list posts to page by count. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @return mixed
     */
    public function getByPage(int $numberOfPostsOnPage = null)
    {
        $posts = $this->with(['user:id,name,avatar,avatar_url'])
                        ->withCount('comments')
                        ->orderBy('created_at', 'desc')
                        ->paginate($numberOfPostsOnPage);

        return $posts;
    }

    /**
     * Get list posts. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @param int|null $lengthDescription
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getList(PostFilter $filters)
    {
        $posts = Post::filter($filters)
            ->with('user:id,name,avatar,avatar_url')
            ->orderBy('posts.created_at', 'desc')
            ->paginate(config('view.posts'));

        return $posts;
    }

    /**
     * Get post by id with relations comments, user and users comments.
     *
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function getByIdWithRelations(int $id)
    {
        try {
            $post = $this->with(['user:id,name,avatar,avatar_url'])
                            ->find($id);
        } catch (\Exception $e) {
            throw $e;
        }

        return $post;
    }

    /**
     * Get list posts latest. With pagination.
     *
     * @param int|null $latestPostsNumber
     * @return Post[]|Collection
     */
    public function getLatestWithRelations(int $latestPostsNumber = null)
    {
        $latestPosts = Post::with('user:id,name,avatar,avatar_url')
                            ->withCount('comments')
                            ->limit($latestPostsNumber)
                            ->orderByDesc('created_at')
                            ->get();

        return $latestPosts;
    }

    /**
     * Search by Description like and Title like. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @return mixed
     */
    public function searchAll(int $numberOfPostsOnPage = null)
    {
        $posts = $this->orderBy('id', 'desc')
                        ->with(['user:id,name,avatar,avatar_url'])
                        ->withCount('comments')
                        ->paginate($numberOfPostsOnPage);

        return $posts;
    }

    /**
     * Get post in short view for notification by id
     *
     * @param int $id
     * @return Post
     */
    public function getForNotificationById($id)
    {
        $post = Post::select('posts.id', 'posts.title', DB::raw("'post' as type"), 'posts.user_id')
            ->with('user:id,name')
            ->findOrFail($id);

        return $post;
    }

    /**
     * Create new post
     *
     * @param array $attributes
     *
     * @param array $attributes
     * @return Post|\Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Exception
     */
    public function createWithCover(array $attributes)
    {
        $attributes['user_id'] = \Auth::id();

        $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_CREATE);

        $this->putCover($attributes);

        try{
            $post = Post::create($attributes);
        }
        catch (\Exception $e) {

            $this->removeCover($attributes['avatar']);

            throw $e;
        }

        return $post;
    }

    /**
     * Update cover
     *
     * @param array $attributes
     */
    protected function putCover(array &$attributes)
    {
        if (isset($attributes['cover'])) {
            $attributes['cover'] = \Storage::disk(env('FILESYSTEM_DRIVER'))->putFile('public/uploads/posts', $attributes['cover']);
            $attributes['cover_url'] = \Storage::url($attributes['cover']);

            return true;
        }

        return false;
    }

    /**
     * Remove cover
     *
     * @param string|null $cover
     */
    public function removeCover(string $cover = null)
    {
        if ($cover) {
            \Storage::disk(env('FILESYSTEM_DRIVER'))->delete($cover);
        }
    }

    /**
     * Update post by post and attributes
     *
     * @param Post $post
     * @param array $attributes
     * @return Post|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Exception
     */
    public function updateByPost(Post $post, array $attributes)
    {
        $this->validator->setId($post->id);
        $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_UPDATE);

        $oldCover = $post->cover;
        $coverUpdate = $this->putCover($attributes);

        if ($coverUpdate === true) {
            $this->removeCover($oldCover);
        }

        try{
            $post->update($attributes);
        }
        catch (\Exception $e) {

            $this->removeCover($attributes['cover']);

            throw $e;
        }

        return $post;
    }

    /**
     * Remove post cover
     *
     * @param \App\Models\Post $post
     */
    public function removePostCover(Post $post)
    {
        $this->removeCover($post->cover);

        $post->update(['cover' => null, 'cover_url' => null]);
    }
}
