<?php

namespace App\Models;

use App\Models\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Models\Office
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Office whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $floors
 * @property-read int|null $floors_count
 */
class Office extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany('App\Models\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function floors()
    {
        return $this->hasMany('App\Models\Category')
            ->whereNull('parent');
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
