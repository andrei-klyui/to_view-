<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class PostValidator.
 *
 * @package namespace App\Validators;
 */
class PostValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title'         => 'required|filled|max:191',
            'description'   => 'required|filled',
            'cover'         => 'mimes:jpeg,jpg,png,bmp,gif|max:4096',
        ],

        ValidatorInterface::RULE_UPDATE => [
            'title'         => 'required|filled|max:191',
            'description'   => 'required|filled',
            'cover'         => 'mimes:jpeg,jpg,png,bmp,gif|max:4096',
        ],
    ];
}
