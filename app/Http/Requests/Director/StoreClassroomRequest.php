<?php

namespace App\Http\Requests\Director;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'point_of_school_id' => ['required', 'integer', Rule::exists('point_of_schools', 'id')],
            'teacher_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', Rule::unique('classrooms', 'code')->where('school_id', $this->user()?->school_id)],
            'student_ids' => ['array'],
            'student_ids.*' => ['integer', Rule::exists('users', 'id')],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $allowedPointIds = $this->user()?->pointOfSchools()->pluck('point_of_schools.id')->map(fn ($id) => (int) $id)->all() ?? [];
                $pointId = (int) $this->input('point_of_school_id');

                if ($pointId && ! in_array($pointId, $allowedPointIds, true)) {
                    $validator->errors()->add('point_of_school_id', 'Selecione um ponto de ensino disponivel para a diretoria.');
                }

                $teacherId = $this->integer('teacher_id');

                if ($teacherId) {
                    $teacher = User::query()->find($teacherId);

                    if (! $teacher || ! $teacher->hasRole(RoleEnum::TEACHER) || ! $teacher->pointOfSchools()->where('point_of_schools.id', $pointId)->exists()) {
                        $validator->errors()->add('teacher_id', 'Selecione um professor vinculado ao ponto de ensino informado.');
                    }
                }

                $studentIds = collect($this->input('student_ids', []))->map(fn ($id) => (int) $id)->filter()->values();

                if ($studentIds->isEmpty()) {
                    return;
                }

                $validStudentsCount = User::query()
                    ->whereIn('id', $studentIds)
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->where('point_of_schools.id', $pointId))
                    ->count();

                if ($validStudentsCount !== $studentIds->count()) {
                    $validator->errors()->add('student_ids', 'Selecione apenas alunos vinculados ao ponto de ensino informado.');
                }
            },
        ];
    }
}
