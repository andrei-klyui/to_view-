<?php

namespace App\Models;

use App\Traits\GetImageUrlTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;
use Prettus\Repository\Contracts\Transformable;

/**
 * App\Models\Attachment
 *
 * @property int $id
 * @property string $title
 * @property string $filename
 * @property string $type
 * @property string $size
 * @property string $mime
 * @property int $issue_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property mixed|string $file_url
 * @property-read string $image
 * @property-read string $image_date
 * @property-read int $image_issue_id
 * @property-read string $image_title
 * @property-read string $is_image
 */
class Attachment extends Model implements Transformable
{
    use TransformableTrait, GetImageUrlTrait;

    /**
     * @var array
     */
    protected $fillable = ['title', 'filename', 'file_url', 'type', 'size', 'mime', 'issue_id'];

    /**
     * @var array
     */
    protected $appends = ['image', 'is_image', 'image_title', 'image_date'];

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete()
    {
        \Storage::delete($this->filename);

        return parent::delete();
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImageAttribute(): string
    {
        $parts = explode("/", $this->mime);
        if ($parts[0] !== 'image') {
            return \Storage::url('no-image.png');
        }

        return $this->file_url;
    }

    /**
     * Get is image
     *
     * @return string
     */
    public function getIsImageAttribute(): string
    {
        $parts = explode("/", $this->mime);
        if ($parts[0] !== 'image') {
            return false;
        }
        return true;
    }

    /**
     * Get image title
     *
     * @return string
     */
    public function getImageTitleAttribute(): string
    {
        return $this->title;
    }

    /**
     * Get image date
     *
     * @return string
     */
    public function getImageDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('d-m-Y');
    }

    /**
     * @return int
     */
    public function getImageIssueIdAttribute(): int
    {
        return $this->issue_id;
    }

    /**
     * Return url image with hostname (http[s]://hostname/....jpg)
     *
     * @param $value
     * @return mixed|string
     */
    public function getFileUrlAttribute($value)
    {
        return $this->getImageUrl($value);
    }
}
