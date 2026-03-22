<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', Rule::exists('roles', 'name')],
            'school_id' => ['nullable', 'integer', Rule::exists('schools', 'id')],
        ];
    }
}
