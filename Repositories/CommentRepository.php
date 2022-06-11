<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CommentRepository.
 *
 * @package namespace App\Repositories;
 */
interface CommentRepository extends RepositoryInterface
{
    /**
     * Get list comments by commentableType and commentableId
     *
     * @param $commentableType
     * @param int $commentableId
     * @param int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByEntityId($commentableType, int $commentableId, int $limit = null);

    /**
     * @param $id
     *
     * @return int
     */
    public function delete($id): int;
}
