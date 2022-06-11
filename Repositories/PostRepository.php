<?php

namespace App\Repositories;

use App\Models\Filters\PostFilter;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface PostRepository.
 *
 * @package namespace App\Repositories;
 */
interface PostRepository extends RepositoryInterface
{
    /**
     * Get list posts to page by count. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @return mixed
     */
    public function getByPage(int $numberOfPostsOnPage = null);

    /**
     * Get post by id with relations comments, user and users comments.
     *
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function getByIdWithRelations(int $id);


    /**
     * Get list posts latest. With pagination.
     *
     * @param int|null $latestPostsNumber
     * @return Post[]|Collection
     */
    public function getLatestWithRelations(int $latestPostsNumber = null);

    /**
     * Search by Description like and Title like. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @return mixed
     */
    public function searchAll(int $numberOfPostsOnPage = null);

    /**
     * Get post in short view for notification by id
     *
     * @param int $id
     * @return Post
     */
    public function getForNotificationById($id);

    /**
     * Get list posts. With pagination.
     *
     * @param int|null $numberOfPostsOnPage
     * @param int|null $lengthDescription
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getList(PostFilter $filters);

    /**
     * Create new post
     *
     * @param array $attributes
     *
     * @param array $attributes
     * @return Post|\Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Exception
     */
    public function createWithCover(array $attributes);

    /**
     * Update post by post and attributes
     *
     * @param Post $post
     * @param array $attributes
     * @return Post|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Exception
     */
    public function updateByPost(Post $post, array $attributes);

    /**
     * Remove post cover
     *
     * @param \App\Models\Post $post
     */
    public function removePostCover(Post $post);
}
