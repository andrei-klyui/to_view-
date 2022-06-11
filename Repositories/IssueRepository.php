<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Filters\IssueFilter;
use App\Models\Issue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface IssueRepository.
 *
 * @package namespace App\Repositories;
 */
interface IssueRepository extends RepositoryInterface
{
    /**
     * Get list Issues by filters.
     * Depending on the flag withArchived with archival tasks or without them.
     *
     * @param IssueFilter $filters
     * @param bool $withArchived
     * @param int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getByFilters(IssueFilter $filters, bool $withArchived = false, int $limit = null);

    /**
     * Get list Issues by filters and categoryId.
     * Depending on the flag withArchived with archival tasks or without them.
     *
     * @param IssueFilter $filters
     * @param int $categoryId
     * @param bool $withArchived
     * @param int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByFiltersAndCategory(
        IssueFilter $filters,
        int $categoryId,
        bool $withArchived = false,
        int $limit = null
    );

    /**
     * Get archived list Issues by filters.
     *
     * @param IssueFilter $filters
     * @param int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getArchivedByFilters(IssueFilter $filters, int $limit = null);

    /**
     * @param int $id
     * @return mixed
     */
    public function archivedUpdateId($id);

    /**
     * Get Issue by id with category
     *
     * @param int $id
     * @return Issue
     * @throws \Exception
     */
    public function getByIdWithCategory(int $id);

    /**
     * Patch Issue by 1 or more fields
     *
     * @param $attributes
     * @return mixed
     * @throws ValidatorException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function patch($attributes);

    /**
     * Get issue in short view for notification by id
     *
     * @param int $id
     * @return Issue
     */
    public function getForNotificationById($id);

    /**
     * @param Issue $issue
     * @return mixed
     */
    public function removeIssueImage(Issue $issue);
}
