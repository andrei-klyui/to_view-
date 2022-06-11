<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class ExecutorValidator.
 *
 * @package namespace App\Validators;
 */
class ExecutorValidator extends LaravelValidator
{
    const RULE_CREATE_NEW_EXECUTOR = 'create_new_executor';
    const RULE_UPDATE_EXECUTOR = 'update_executor';

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ExecutorValidator::RULE_CREATE_NEW_EXECUTOR => [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|max:255|email',
        ],
        ExecutorValidator::RULE_UPDATE_EXECUTOR => [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|max:255|email',
        ],
    ];
}
