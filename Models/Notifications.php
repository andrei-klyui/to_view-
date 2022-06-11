<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Notifications.
 *
 * @package namespace App\Models;
 * @property int $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $data
 * @property string|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notifications whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Notifications extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
