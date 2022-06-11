<?php


namespace App\Traits;


use Illuminate\Support\Facades\Storage;

trait GetImageUrlTrait
{
    /**
     * Return url image with hostname (http[s]://hostname/....jpg) or empty string
     *
     * @param $value
     * @return string
     */
    public function getImageUrl($value)
    {
        $regex = "/^http[s]?:\/\/.*?/i";

        if (preg_match($regex, $value)) {
            return $value;
        }

        return $value ? Storage::disk('img')->url($value) : '';
    }
}
