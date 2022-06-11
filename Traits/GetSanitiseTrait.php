<?php


namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait GetSanitiseTrait
{
    /** @var  Builder */
    protected $builder;

    /**
     * Escape symbols like %, _, \\
     *
     * @param string $value
     * @param string $char
     * @return string
     */
    public function sanitizeLike(string $value, string $char = '\\'): string
    {
        return str_replace([$char, '%', '_'], [$char . $char, $char . '%', $char . '_'], $value);
    }
}
