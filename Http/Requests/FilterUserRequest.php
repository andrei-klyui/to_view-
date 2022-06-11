<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FilterUserRequest
 * @package App\Http\Requests
 */
class FilterUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => 'string|max:50',
            'email' => 'string|max:50', // no need to add an email, otherwise it will not search by part of the request
            'role' => 'string|max:50'
        ];
    }
}
