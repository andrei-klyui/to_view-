<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Utils\Helpers\FactoringHelper;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * \App\Models\Issue
 *
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property int $executor_id
 * @property string $status
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $priority
 * @property string|null $due_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Executor $executor
 * @property-read int $issue_user_id
 * @property-read \App\Models\User $reporter
 * @property-read \App\Models\User $user
 * @method static Builder|Issue filter(\App\Models\Filters\QueryFilter $filters)
 * @method static Builder|Issue newModelQuery()
 * @method static Builder|Issue newQuery()
 * @method static Builder|Issue query()
 * @method static Builder|Issue whereCategoryId($value)
 * @method static Builder|Issue whereCreatedAt($value)
 * @method static Builder|Issue whereDescription($value)
 * @method static Builder|Issue whereDueDate($value)
 * @method static Builder|Issue whereExecutorId($value)
 * @method static Builder|Issue whereId($value)
 * @method static Builder|Issue wherePriority($value)
 * @method static Builder|Issue whereStatus($value)
 * @method static Builder|Issue whereTitle($value)
 * @method static Builder|Issue whereUpdatedAt($value)
 * @method static Builder|Issue whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $reporter_id
 * @method static Builder|Issue whereReporterId($value)
 */
class Issue extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'title',
        'description',
        'status',
        'type',
        'priority',
        'executor_id',
        'category_id',
        'user_id',
        'due_date',
        'reporter_id',
        'parent_id'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in progress';
    const STATUS_CLOSED = 'closed';
    const STATUS_DONE = 'done';
    const STATUS_ARCHIVED = 'archive';

    const TYPE_ELECTRICITY = 'electricity';
    const TYPE_PLUMBING = 'plumbing';
    const TYPE_OFFICE = 'office';
    const TYPE_OTHER = 'other';

    const PRIORITY_TRIVIAL = 'trivial';
    const PRIORITY_LOW = 'low';
    const PRIORITY_MAJOR = 'major';
    const PRIORITY_CRITICAL = 'critical';

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();
        self::saving(function (Issue $model) {
            $officeManagerExecutorId = FactoringHelper::getDefaultExecutorId();

            $model->status = isset($model->status) ? $model->status : self::STATUS_PENDING;
            $model->executor_id = isset($model->executor_id) ? $model->executor_id : $officeManagerExecutorId;
        });
        static::deleting(function (Issue $issue) {
            $issue->comments()->delete();

            $attachments = $issue->attachments;
            foreach ($attachments as $attachment) {
                /** @var Attachment $attachment */
                $attachment->delete();
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function executor(): BelongsTo
    {
        return $this->belongsTo(Executor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id', 'id');
    }

    /**
     * @return array
     */
    public static function priorities(): array
    {
        $result = [];
        foreach (Issue::prioritiesOrigin() as $priority) {
            $result[$priority] = trans('priority.' . $priority);
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function prioritiesOrigin(): array
    {
        return [
            Issue::PRIORITY_TRIVIAL,
            Issue::PRIORITY_LOW,
            Issue::PRIORITY_MAJOR,
            Issue::PRIORITY_CRITICAL
        ];
    }

    /**
     * @return Comment[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    protected function deleteComments()
    {
        return $this->comments ?? $this->comments()->delete();
    }

    /**
     * @return array
     */
    public static function statuses(): array
    {
        $result = [];
        foreach (Issue::statusesOrigin() as $status) {
            $result[$status] = trans('status.' . $status);
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function statusesOrigin(): array
    {
        return [
            Issue::STATUS_PENDING,
            Issue::STATUS_IN_PROGRESS,
            Issue::STATUS_CLOSED,
            Issue::STATUS_DONE,
        ];
    }

    /**
     * @return array
     */
    public static function types(): array
    {
        $result = [];
        foreach (Issue::typesOrigin() as $type) {
            $result[$type] = trans('type.' . $type);
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function typesOrigin(): array
    {
        return [
            Issue::TYPE_ELECTRICITY,
            Issue::TYPE_PLUMBING,
            Issue::TYPE_OFFICE,
            Issue::TYPE_OTHER
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return FactoringHelper::makeShort($this->description, 75);
    }

    /**
     * @return string
     */
    public function createdAt(): string
    {
        return FactoringHelper::localizeDate($this->created_at);
    }

    /**
     * @return string
     */
    public function dueDate(): string
    {
        return FactoringHelper::localizeDate($this->due_date);
    }

    /**
     * @param Builder $query
     * @param QueryFilter $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, QueryFilter $filters): Builder
    {
        return $filters->apply($query);
    }

    /**
     * @return int
     */
    public function getIssueUserIdAttribute(): int
    {
        return $this->user_id;
    }

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Issue::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Issue::class, 'parent_id');
    }
}
