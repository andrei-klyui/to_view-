<?php

namespace App\Validators;

use \Prettus\Validator\LaravelValidator;
use \Prettus\Validator\Contracts\ValidatorInterface;
use App\Models\Issue;
use \Illuminate\Contracts\Validation\Factory;

/**
 * Class IssuePatchValidator.
 *
 * @package namespace App\Validators;
 */
class IssuePatchValidator extends LaravelValidator
{

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [],

        ValidatorInterface::RULE_UPDATE => [
            'status'        => 'sometimes',
            'priority'      => 'sometimes',
            'type'          => 'sometimes',
            'title'         => 'sometimes|filled|max:255',
            'description'   => 'sometimes|filled',
            'category_id'   => 'sometimes|numeric',
            'reporter_id'   => 'sometimes|numeric',
            'due_date'      => 'sometimes|date',
            'executor_id'   => 'sometimes|numeric'
        ],
    ];

    /**
     * Construct
     *
     * @param Factory $validator
     */
    public function __construct(Factory $validator)
    {
        parent::__construct($validator);

        $this->rules[ValidatorInterface::RULE_UPDATE]['status']
            .= '|in:' . implode(",", Issue::statusesOrigin());
        $this->rules[ValidatorInterface::RULE_UPDATE]['priority']
            .= '|in:' . implode(",", Issue::prioritiesOrigin());
        $this->rules[ValidatorInterface::RULE_UPDATE]['type']
            .= '|in:' . implode(",", Issue::typesOrigin());
    }
}
