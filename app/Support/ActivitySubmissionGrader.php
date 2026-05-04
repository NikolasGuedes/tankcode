<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\ActivityQuestion;

class ActivitySubmissionGrader
{
    /**
     * @param  array<int, array<string, mixed>>  $answers
     * @return array{
     *     score: float,
     *     total_points: float,
     *     correct_answers_count: int,
     *     answers: array<int, array{
     *         question_id: int,
     *         answer_payload: array<string, mixed>,
     *         is_correct: bool,
     *         earned_points: float
     *     }>
     * }
     */
    public function grade(Activity $activity, array $answers): array
    {
        $answersByQuestion = collect($answers)->keyBy(fn (array $answer) => (int) $answer['question_id']);
        $pointsPerQuestion = (float) $activity->points_per_question;
        $gradedAnswers = [];
        $correctAnswersCount = 0;
        $score = 0.0;

        foreach ($activity->questions as $question) {
            $submittedAnswer = $answersByQuestion->get($question->id, []);
            $normalizedAnswer = $this->normalizeAnswer($question, $submittedAnswer);
            $isCorrect = $this->isCorrect($question, $normalizedAnswer);
            $earnedPoints = $isCorrect ? $pointsPerQuestion : 0.0;

            $gradedAnswers[] = [
                'question_id' => $question->id,
                'answer_payload' => $normalizedAnswer,
                'is_correct' => $isCorrect,
                'earned_points' => $earnedPoints,
            ];

            if ($isCorrect) {
                $correctAnswersCount++;
                $score += $earnedPoints;
            }
        }

        return [
            'score' => $score,
            'total_points' => (float) $activity->total_points,
            'correct_answers_count' => $correctAnswersCount,
            'answers' => $gradedAnswers,
        ];
    }

    /**
     * @param  array<string, mixed>  $answer
     * @return array<string, mixed>
     */
    private function normalizeAnswer(ActivityQuestion $question, array $answer): array
    {
        if ($question->type === 'multiple_choice') {
            return [
                'selected_option' => $this->normalizeOption($answer['selected_option'] ?? null),
            ];
        }

        if ($question->type === 'drag_drop') {
            return [
                'blanks' => collect($answer['blanks'] ?? [])
                    ->mapWithKeys(fn ($value, $key) => [(string) $key => $this->normalizeBlankValue($question, $value)])
                    ->all(),
            ];
        }

        return [
            'pairs' => collect($answer['pairs'] ?? [])
                ->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $answer
     */
    private function isCorrect(ActivityQuestion $question, array $answer): bool
    {
        if ($question->type === 'multiple_choice') {
            return ($answer['selected_option'] ?? null) === $question->correct_option;
        }

        if ($question->type === 'drag_drop') {
            $expectedAnswers = collect($question->blank_answers ?? [])
                ->mapWithKeys(function ($keywordIndex, $blankKey) use ($question) {
                    $keyword = $question->keywords[(int) $keywordIndex] ?? null;

                    return [(string) $blankKey => $this->normalizeString($keyword)];
                })
                ->all();

            return ($answer['blanks'] ?? []) === $expectedAnswers;
        }

        $expectedPairs = collect($question->pairs ?? [])
            ->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])
            ->all();

        return ($answer['pairs'] ?? []) === $expectedPairs;
    }

    private function normalizeBlankValue(ActivityQuestion $question, mixed $value): ?string
    {
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            $keyword = $question->keywords[(int) $value] ?? null;

            return $this->normalizeString($keyword);
        }

        return $this->normalizeString($value);
    }

    private function normalizeString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        return mb_strtolower($normalized);
    }

    private function normalizeOption(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = strtoupper(trim($value));

        return $normalized === '' ? null : $normalized;
    }
}
