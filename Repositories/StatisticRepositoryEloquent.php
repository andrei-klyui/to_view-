<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Issue;
use App\Models\Statistic;
use Illuminate\Support\Facades\DB;

/**
 * Class StatisticRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StatisticRepositoryEloquent extends BaseRepository implements StatisticRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Statistic::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get list issues by user id with count and group by priority.
     *
     * @param $userId
     * @return Issue[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCountIssuesByPriorities($userId)
    {
        $counts = Issue::select('priority as item_statistic', DB::raw("count(*) as count"))
            ->where('user_id', '=', $userId)
            ->where('status', '<>', 'archive')
            ->groupBy('priority')
            ->get();

        return $counts;
    }

    /**
     * Get list issues by user id with count and group by status.
     *
     * @param $userId
     * @return Issue[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCountIssuesByStatuses($userId)
    {
        $counts = Issue::select('status as item_statistic', DB::raw("count(*) as count"))
            ->where('user_id', '=', $userId)
            ->groupBy('status')
            ->get();

        return $counts;
    }

    /**
     * Get list issues by user id with count and group by categories.
     *
     * @param $userId
     * @return Issue[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCountIssuesByCategories($userId)
    {
        $counts = Issue::select('categories.name as item_statistic', DB::raw("count(*) as count"))
            ->join('categories', 'issues.category_id', '=', 'categories.id')
            ->where('user_id', '=', $userId)
            ->groupBy('category_id')
            ->get();

        return $counts;
    }
}
