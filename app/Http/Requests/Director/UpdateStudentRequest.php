<?php

namespace App\Http\Requests\Director;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $student */
        $student = $this->route('student');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->id)],
            'point_of_school_id' => ['required', 'integer', Rule::exists('point_of_schools', 'id')],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $allowedIds = $this->user()?->pointOfSchools()->pluck('point_of_schools.id')->map(fn ($id) => (int) $id)->all() ?? [];
                $selectedId = (int) $this->input('point_of_school_id');

                if ($selectedId && ! in_array($selectedId, $allowedIds, true)) {
                    $validator->errors()->add('point_of_school_id', 'Selecione um ponto de ensino disponivel para a diretoria.');
                }
            },
        ];
    }
}
