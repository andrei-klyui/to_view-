<?php

namespace App\Validators;

use \Prettus\Validator\LaravelValidator;
use \Prettus\Validator\Contracts\ValidatorInterface;

/**
 * Class IssueValidator.
 *
 * @package namespace App\Validators;
 */
class IssueValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title'         => 'required|filled|max:255',
            'description'   => 'required|filled',
            'category_id'   => 'required|numeric',
            'due_date'      => 'required|date',
            'executor_id'   => 'sometimes|numeric'
        ],

        ValidatorInterface::RULE_UPDATE => [
            'title'         => 'required|filled|max:255',
            'description'   => 'required|filled',
            'category_id'   => 'required|numeric',
            'due_date'      => 'required|date',
            'executor_id'   => 'sometimes|numeric'
        ],
    ];
}
