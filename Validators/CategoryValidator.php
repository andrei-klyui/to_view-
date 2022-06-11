<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class CategoryValidator.
 *
 * @package namespace App\Validators;
 */
class CategoryValidator extends LaravelValidator
{
    const RULE_CREATE_NEW_CATEGORY = 'create_new_category';
    const RULE_UPDATE_CATEGORY = 'update_category';

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        CategoryValidator::RULE_CREATE_NEW_CATEGORY => [
            'name'                  => 'required|string|max:255',
            'parent'              => 'nullable|int|max:255',
            'office_id'             => 'required|numeric',
        ],
        CategoryValidator::RULE_UPDATE_CATEGORY => [
            'name'                  => 'required|string|max:255',
            'parent'              => 'nullable|int|max:255',
            'office_id'             => 'nullable|numeric',
        ],
    ];
}
