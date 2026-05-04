<?php

namespace App\Http\Requests\Student;

use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\ActivityQuestion;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivitySubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleEnum::STUDENT) ?? false;
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.selected_option' => ['nullable', 'string'],
            'answers.*.blanks' => ['nullable', 'array'],
            'answers.*.pairs' => ['nullable', 'array'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $activity = $this->route('activity');

                if (! $activity instanceof Activity) {
                    return;
                }

                $answers = collect($this->input('answers', []));
                $activityQuestions = $activity->questions->keyBy('id');
                $submittedQuestionIds = $answers->pluck('question_id')->filter()->map(fn ($id) => (int) $id);

                if ($submittedQuestionIds->unique()->count() !== $submittedQuestionIds->count()) {
                    $validator->errors()->add('answers', 'Envie apenas uma resposta para cada questão.');
                }

                if ($submittedQuestionIds->sort()->values()->all() !== $activityQuestions->keys()->sort()->values()->all()) {
                    $validator->errors()->add('answers', 'Responda todas as questões da atividade antes de enviar.');
                }

                $answers->values()->each(function (array $answer, int $index) use ($activityQuestions, $validator): void {
                    $question = $activityQuestions->get((int) ($answer['question_id'] ?? 0));

                    if (! $question instanceof ActivityQuestion) {
                        $validator->errors()->add("answers.{$index}.question_id", 'A questão informada não pertence a esta atividade.');

                        return;
                    }

                    $this->validateQuestionAnswer($validator, $question, $answer, $index);
                });
            },
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Responda a atividade antes de enviar.',
            'answers.array' => 'As respostas devem ser enviadas em uma lista válida.',
            'answers.min' => 'Responda pelo menos uma questão.',
            'answers.*.question_id.required' => 'Não foi possível identificar uma das questões respondidas.',
            'answers.*.question_id.integer' => 'Uma das questões enviadas é inválida.',
        ];
    }

    /**
     * @param  array<string, mixed>  $answer
     */
    private function validateQuestionAnswer($validator, ActivityQuestion $question, array $answer, int $index): void
    {
        if ($question->type === 'multiple_choice') {
            if (! in_array($answer['selected_option'] ?? null, ['A', 'B', 'C', 'D'], true)) {
                $validator->errors()->add("answers.{$index}.selected_option", 'Selecione uma alternativa para a questão de múltipla escolha.');
            }

            return;
        }

        if ($question->type === 'drag_drop') {
            $blanks = $answer['blanks'] ?? null;
            $expectedKeys = collect($question->blank_answers ?? [])->keys()->map(fn ($key) => (string) $key)->sort()->values()->all();
            $submittedKeys = collect($blanks ?? [])->keys()->map(fn ($key) => (string) $key)->sort()->values()->all();

            if (! is_array($blanks) || $submittedKeys !== $expectedKeys || collect($blanks)->contains(fn ($value) => blank($value))) {
                $validator->errors()->add("answers.{$index}.blanks", 'Preencha todas as lacunas da questão antes de enviar.');
            }

            return;
        }

        $pairs = $answer['pairs'] ?? null;
        $expectedKeys = collect($question->left_column ?? [])->keys()->map(fn ($key) => (string) $key)->sort()->values()->all();
        $rightIndexes = collect($question->right_column ?? [])->keys()->map(fn ($key) => (string) $key)->all();
        $submittedKeys = collect($pairs ?? [])->keys()->map(fn ($key) => (string) $key)->sort()->values()->all();

        if (! is_array($pairs) || $submittedKeys !== $expectedKeys || collect($pairs)->contains(fn ($value) => ! in_array((string) $value, $rightIndexes, true))) {
            $validator->errors()->add("answers.{$index}.pairs", 'Relacione todos os itens da questão antes de enviar.');
        }
    }
}
