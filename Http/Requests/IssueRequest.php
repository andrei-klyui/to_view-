<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return (bool)$this->user('api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'user_id' => 'integer',
            'category_id' => 'required|integer',
            'reporter_id' => 'integer',
            'executor_id' => 'required|integer',
            'due_date' => 'required|date',
            'description' => 'required|string',
            'status' => 'required|string',
            'type' => 'required|string',
            'priority' => 'required|string',
            'attachment.*' => 'file'
        ];
    }
}
