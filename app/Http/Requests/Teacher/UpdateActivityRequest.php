<?php

namespace App\Http\Requests\Teacher;

use App\Enums\ActivityLevelEnum;
use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\Classroom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->user();
        $activity = $this->route('activity');

        if (! $teacher?->hasRole(RoleEnum::TEACHER) || ! $activity instanceof Activity) {
            return false;
        }

        return $this->activityIsAllowed($activity);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'due_date' => $this->input('due_date') ?: null,
            'status' => $this->input('status') ?: 'draft',
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'string', Rule::in(ActivityLevelEnum::values())],
            'questions_count' => ['required', 'integer', 'min:1'],
            'classroom_id' => ['required', 'integer', Rule::exists('classrooms', 'id')->whereNull('deleted_at')],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(['draft', 'published'])],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $classroomId = $this->integer('classroom_id');

                if ($classroomId && ! $this->classroomIsAllowed($classroomId)) {
                    $validator->errors()->add('classroom_id', 'Selecione uma turma vinculada ao seu perfil de professor.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título da atividade é obrigatório.',
            'title.max' => 'O título da atividade deve ter no máximo 255 caracteres.',
            'level.required' => 'O nível de dificuldade é obrigatório.',
            'level.in' => 'O nível de dificuldade informado é inválido.',
            'questions_count.required' => 'A quantidade de questões é obrigatória.',
            'questions_count.integer' => 'A quantidade de questões deve ser um número inteiro.',
            'questions_count.min' => 'A atividade deve ter pelo menos uma questão.',
            'classroom_id.required' => 'Selecione uma turma para a atividade.',
            'classroom_id.exists' => 'A turma informada não foi encontrada.',
            'due_date.date' => 'A data de entrega deve ser uma data válida.',
            'status.in' => 'O status informado é inválido.',
        ];
    }

    private function activityIsAllowed(Activity $activity): bool
    {
        $teacher = $this->user();
        $pointIds = $teacher?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();
        $classroomIsAllowed = $activity->classroom()
            ->where('teacher_id', $teacher?->id)
            ->where('school_id', $teacher?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->exists();

        return $activity->teacher_id === $teacher?->id || $classroomIsAllowed;
    }

    private function classroomIsAllowed(int $classroomId): bool
    {
        $teacher = $this->user();
        $pointIds = $teacher?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        return Classroom::query()
            ->whereKey($classroomId)
            ->where('teacher_id', $teacher?->id)
            ->where('school_id', $teacher?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->exists();
    }
}
