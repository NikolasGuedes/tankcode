<?php

namespace App\Http\Requests\Teacher;

use App\Enums\ActivityLevelEnum;
use App\Enums\RoleEnum;
use App\Models\Classroom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleEnum::TEACHER) ?? false;
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
            'classroom_id' => ['required', 'integer', Rule::exists('classrooms', 'id')->whereNull('deleted_at')],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(['draft', 'published'])],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.type' => ['required', 'string', Rule::in(['multiple_choice', 'drag_drop', 'matching'])],
            'questions.*.statement' => ['required', 'string'],
            'questions.*.correct_option' => ['nullable', 'string'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.A' => ['nullable', 'string'],
            'questions.*.options.B' => ['nullable', 'string'],
            'questions.*.options.C' => ['nullable', 'string'],
            'questions.*.options.D' => ['nullable', 'string'],
            'questions.*.keywords' => ['nullable', 'array'],
            'questions.*.keywords.*' => ['nullable', 'string'],
            'questions.*.blank_answers' => ['nullable', 'array'],
            'questions.*.left_column' => ['nullable', 'array'],
            'questions.*.left_column.*' => ['nullable', 'string'],
            'questions.*.right_column' => ['nullable', 'array'],
            'questions.*.right_column.*' => ['nullable', 'string'],
            'questions.*.pairs' => ['nullable', 'array'],
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

                $this->validateQuestions($validator);
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
            'classroom_id.required' => 'Selecione uma turma para a atividade.',
            'classroom_id.exists' => 'A turma informada não foi encontrada.',
            'due_date.date' => 'A data de entrega deve ser uma data válida.',
            'status.in' => 'O status informado é inválido.',
            'questions.required' => 'Adicione pelo menos uma questão para a atividade.',
            'questions.array' => 'As questões devem ser enviadas em uma lista válida.',
            'questions.min' => 'Adicione pelo menos uma questão para a atividade.',
            'questions.*.type.required' => 'Selecione o tipo de cada questão.',
            'questions.*.type.in' => 'O tipo de questão informado é inválido.',
            'questions.*.statement.required' => 'O enunciado de cada questão é obrigatório.',
        ];
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

    private function validateQuestions($validator): void
    {
        foreach ($this->input('questions', []) as $index => $question) {
            $type = $question['type'] ?? null;

            if ($type === 'multiple_choice') {
                $options = $question['options'] ?? null;

                if (! is_array($options)) {
                    $validator->errors()->add("questions.{$index}.options", 'Informe as alternativas da questao de multipla escolha.');

                    continue;
                }

                foreach (['A', 'B', 'C', 'D'] as $option) {
                    if (blank($options[$option] ?? null)) {
                        $validator->errors()->add("questions.{$index}.options.{$option}", "A alternativa {$option} e obrigatoria.");
                    }
                }

                if (! in_array($question['correct_option'] ?? null, ['A', 'B', 'C', 'D'], true)) {
                    $validator->errors()->add("questions.{$index}.correct_option", 'Selecione a alternativa correta da questao de multipla escolha.');
                }
            }

            if ($type === 'drag_drop') {
                $keywords = $question['keywords'] ?? null;
                $blankAnswers = $question['blank_answers'] ?? null;

                if (! is_array($keywords) || collect($keywords)->filter(fn ($keyword) => filled($keyword))->isEmpty()) {
                    $validator->errors()->add("questions.{$index}.keywords", 'Informe ao menos uma palavra-chave para a questao de arrastar e soltar.');
                }

                if (! is_array($blankAnswers) || empty($blankAnswers)) {
                    $validator->errors()->add("questions.{$index}.blank_answers", 'Relacione as lacunas com as palavras corretas.');
                }
            }

            if ($type === 'matching') {
                $leftColumn = $question['left_column'] ?? null;
                $rightColumn = $question['right_column'] ?? null;
                $pairs = $question['pairs'] ?? null;

                if (! is_array($leftColumn) || collect($leftColumn)->filter(fn ($item) => filled($item))->isEmpty()) {
                    $validator->errors()->add("questions.{$index}.left_column", 'Informe os itens da coluna esquerda.');
                }

                if (! is_array($rightColumn) || collect($rightColumn)->filter(fn ($item) => filled($item))->isEmpty()) {
                    $validator->errors()->add("questions.{$index}.right_column", 'Informe os itens da coluna direita.');
                }

                if (! is_array($pairs) || empty($pairs)) {
                    $validator->errors()->add("questions.{$index}.pairs", 'Relacione os pares corretos da questao.');
                }
            }
        }
    }
}
