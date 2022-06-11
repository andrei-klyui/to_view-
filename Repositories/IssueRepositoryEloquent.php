<?php

namespace App\Repositories;

use phpDocumentor\Reflection\Types\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Issue;
use App\Validators\IssueValidator;
use App\Validators\IssuePatchValidator;
use App\Models\Filters\IssueFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\CheckDateWithTimeTrait;

/**
 * Class IssueRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class IssueRepositoryEloquent extends BaseRepository implements IssueRepository
{
    use CheckDateWithTimeTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Issue::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return IssueValidator::class;
    }

    /**
     * Save a new entity in repository
     *
     * @param  array $attributes
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     */
    public function create(array $attributes)
    {
        $attributes['user_id'] = Auth::id();
        $attributes['updated_at'] = now();
        $attributes['due_date'] = $this->getDateWithTime($attributes['due_date']);

        if (isset($attributes['issue_id'])) {
            $attributes['parent_id'] = $attributes['issue_id'];
            unset($attributes['issue_id']);
        }

        return parent::create($attributes);
    }

    /**
     * Update a entity in repository by id
     *
     * @param  array $attributes
     * @param  null|int $id
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     */
    public function update(array $attributes, $id = null)
    {
        $id = $id ?? $attributes['id'];

        $attributes['updated_at'] = now();
        $attributes['due_date'] = $this->getDateWithTime($attributes['due_date']);

        return parent::update($attributes, $id);
    }

    /**
     * Get list Issues by filters.
     * Depending on the flag withArchived with archival tasks or without them.
     *
     * @param  IssueFilter $filters
     * @param  bool $withArchived
     * @param  int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getByFilters(IssueFilter $filters, bool $withArchived = false, int $limit = null)
    {
        $query = $this->getQueryWithFilters($filters)
            ->with('user')
            ->with('executor')
            ->with('reporter');

        if (!$withArchived) {
            $query->where('status', '<>', Issue::STATUS_ARCHIVED);
        }

        $issues = $query->paginate($limit);

        return $issues;
    }

    /**
     * @param  IssueFilter $filters
     * @return Issue|Builder
     */
    protected function getQueryWithFilters(IssueFilter $filters)
    {
        return Issue::filter($filters)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at');
    }

    /**
     * Get list Issues by filters and categoryId.
     * Depending on the flag withArchived with archival tasks or without them.
     *
     * @param  IssueFilter $filters
     * @param  int $categoryId
     * @param  bool $withArchived
     * @param  int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByFiltersAndCategory(
        IssueFilter $filters,
        int $categoryId,
        bool $withArchived = false,
        int $limit = null
    )
    {
        $query = $this->getQueryWithFilters($filters)
            ->where('category_id', '=', $categoryId)
            ->with('user:id,name,avatar', 'category:id,name')
            ->with('executor');

        if (!$withArchived) {
            $query->where('status', '<>', Issue::STATUS_ARCHIVED);
        }

        $issues = $query->paginate($limit);

        return $issues;
    }

    /**
     * Get archived list Issues by filters.
     *
     * @param  IssueFilter $filters
     * @param  int|null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getArchivedByFilters(IssueFilter $filters, int $limit = null)
    {
        $issues = $this->getQueryWithFilters($filters)
            ->with(['user:id,name'])
            ->with('executor')
            ->where('status', Issue::STATUS_ARCHIVED)
            ->addSelect(DB::raw("*, '" . trans('status.' . Issue::STATUS_ARCHIVED) . "' as status"))
            ->paginate($limit);

        return $issues;
    }

    /**
     * Archive issue entity in repository by id
     *
     * @param  int $id
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     */
    public function archivedUpdateId($id)
    {
        $issue = Issue::find($id);

        if ($issue->status == Issue::STATUS_ARCHIVED) {
            $issue->status = Issue::STATUS_PENDING;
        } else {
            $issue->status = Issue::STATUS_ARCHIVED;
        }

        $issue->save();

        return $issue;
    }

    /**
     * Get Issue by id with category
     *
     * @param  int $id
     * @return Issue
     * @throws \Exception
     */
    public function getByIdWithCategory(int $id): Issue
    {
        $issue = Issue::with(['user:id,name', 'reporter:id,name'])->findOrFail($id);
        $issue->loadMissing('category');

        return $issue;
    }

    /**
     * Patch Issue by 1 or more fields
     *
     * @param $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function patch($attributes)
    {
        $this->makeValidator(IssuePatchValidator::class);
        return $this->update($attributes);
    }

    /**
     * Get issue in short view for notification by id
     *
     * @param  int $id
     * @return Issue
     */
    public function getForNotificationById($id)
    {
        $issue = Issue::select('issues.id', 'issues.title', DB::raw("'issue' as type"), 'issues.user_id')
            ->with('user:id,name')
            ->findOrFail($id);

        return $issue;
    }

    /**
     * @param array|null $issue
     */
    public function removeImage($images)
    {
        foreach ($images as $image) {
            \Storage::disk(env('FILESYSTEM_DRIVER'))->delete($image->filename);
        }
    }

    /**
     * @param Issue $issue
     * @return mixed
     */
    public function removeIssueImage(Issue $issue)
    {
        $images = $issue->attachments()->get();

        if ($images) {
            $this->removeImage($images);
        }

        $issue->attachments()->delete();

        return $this->responseStructured
            ->addMessage(trans('issue.image.success.remove'), 'success')
            ->setStatus(true)
            ->getResponse();
    }
}
