<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class RoleValidator.
 *
 * @package namespace App\Validators;
 */
class RoleValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title'         => 'required|filled|max:40|unique:roles',
            'name'   => 'required|filled|max:40',
            'guard_name' => 'filled|max:15',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'title'         => 'required|filled|max:40',
            'name'   => 'required|filled|max:40',
            'guard_name' => 'filled|max:15',
        ],
    ];
}
