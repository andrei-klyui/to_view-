<?php

namespace App\Models\Filters;

use App\Traits\GetSanitiseTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    use GetSanitiseTrait;

    /** @var Request */
    protected $request;

    /** @var  Builder */
    protected $builder;

    /**
     * QueryFilter constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $name = camel_case($name);

            if (!method_exists($this, $name)) {
                continue;
            }

            if ('' !== $value) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }

        return $this->builder;
    }

    /**
     * @return array
     */
    public function filters(): array
    public function filters(): array
    {
        return array_filter($this->request->all(), function ($input) {
            return is_array($input) ? array_filter($input, 'strlen') : strlen($input);
        });
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * SQL 'LIKE' request $value with sanitize trait
     *
     * @param string $tableField
     * @param string $value
     */
    public function whereLike(string $tableField, string $value)
    {
        $value = $value ? $this->sanitizeLike($value) : '';

        if ($tableField && $value) {
            $this->builder->where($tableField, 'LIKE', '%' . $value . '%');
        }
    }
}
