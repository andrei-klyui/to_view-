<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface StatisticRepository.
 *
 * @package namespace App\Repositories;
 */
interface StatisticRepository extends RepositoryInterface
{
    /**
     * Get list issues by user id with count and group by priority.
     *
     * @param $userId
     * @return \App\Models\Issue[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCountIssuesByPriorities($userId);

    /**
     * Get list issues by user id with count and group by status.
     *
     * @param $userId
     * @return \App\Models\Issue[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCountIssuesByStatuses($userId);

    /**
     * Get list issues by user id with count and group by categories.
     *
     * @param $userId
     * @return \App\Models\Issue[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCountIssuesByCategories($userId);
}
