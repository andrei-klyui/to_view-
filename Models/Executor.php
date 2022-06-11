<?php

namespace App\Models;

use App\Models\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Models\Executor
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Executor whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Issue[] $issues
 * @property-read int|null $issues_count
 * @method static Builder|Executor filter(\App\Models\Filters\QueryFilter $filters)
 */
class Executor extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['name', 'description', 'email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues()
    {
        return $this->hasMany('App\Models\Issue');
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
}
