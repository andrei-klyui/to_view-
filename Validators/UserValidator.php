<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class UserValidator.
 *
 * @package namespace App\Validators;
 */
class UserValidator extends LaravelValidator
{

    const RULE_CREATE_NEW_USER = 'create_new_user';
    const RULE_UPDATE_USER = 'update_user';

    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name'                  => 'required|string|max:255',
            'username'              => 'required|string|max:255|unique:users',
            'email'                 => 'required|string|max:255|email|unique:users',
            'password'              => 'required|string|min:6|max:255|confirmed',
            'office_id'             => 'nullable|numeric',
        ],

        ValidatorInterface::RULE_UPDATE => [
            'name'                  => 'required|string|max:255',
            'username'              => 'required|string|max:255|unique:users',
            'email'                 => 'required|string|max:255|email|unique:users',
            'password'              => 'nullable|string|min:6|max:255|confirmed',
            'avatar'                => 'mimes:jpeg,jpg,png,bmp,gif|max:4096',
            'office_id'             => 'nullable|numeric',
        ],

        UserValidator::RULE_CREATE_NEW_USER => [
            'name'                  => 'required|string|max:255',
            'avatar'                => 'mimes:jpeg,jpg,png,bmp,gif|max:4096',
            'username'              => 'required|string|max:255|unique:users',
            'email'                 => 'required|string|max:255|email|unique:users',
            'password'              => 'required|string|min:6|max:255|confirmed',
            'office_id'             => 'nullable|numeric',
            'role_id'               => 'required|numeric',
        ],

        UserValidator::RULE_UPDATE_USER => [
            'name'                  => 'required|string|max:255',
            'avatar'                => 'mimes:jpeg,jpg,png,bmp,gif|max:4096',
            'username'              => 'required|string|max:255|unique:users',
            'email'                 => 'required|string|max:255|email|unique:users',
            'password'              => 'nullable|string|min:6|max:255|confirmed',
            'office_id'             => 'nullable|numeric',
        ],
    ];
}
