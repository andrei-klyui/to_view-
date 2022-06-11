<?php

namespace App\Models;

use App\Models\Filters\QueryFilter;
use App\Traits\GetImageUrlTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property string|null $cover
 * @property string $description
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereCoverUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereUserId($value)
 * @mixin \Eloquent
 * @property mixed|string $cover_url
 * @property-read int|null $comments_count
 */
class Post extends Model implements Transformable
{
    use TransformableTrait, GetImageUrlTrait;

    protected $fillable = [
        'title',
        'cover',
        'cover_url',
        'description',
        'user_id'
    ];

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function (Post $post) {
            $post->comments()->delete();
        });
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete()
    {
        \Storage::delete($this->cover);

        return parent::delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Return url image with hostname (http[s]://hostname/....jpg)
     *
     * @param $value
     * @return mixed|string
     */
    public function getCoverUrlAttribute($value)
    {
        return $this->getImageUrl($value);
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
