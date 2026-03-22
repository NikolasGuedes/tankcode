<?php

namespace App\Http\Requests\Director;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $teacher */
        $teacher = $this->route('teacher');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'point_of_school_ids' => ['required', 'array', 'min:1'],
            'point_of_school_ids.*' => ['integer', Rule::exists('point_of_schools', 'id')],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $allowedIds = $this->user()?->pointOfSchools()->pluck('point_of_schools.id')->map(fn ($id) => (int) $id)->all() ?? [];
                $selectedIds = collect($this->input('point_of_school_ids', []))->map(fn ($id) => (int) $id)->filter()->values();

                if ($selectedIds->diff($allowedIds)->isNotEmpty()) {
                    $validator->errors()->add('point_of_school_ids', 'Selecione apenas pontos de ensino disponiveis para a diretoria.');
                }
            },
        ];
    }
}
