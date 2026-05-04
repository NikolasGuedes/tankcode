<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreActivitySubmissionRequest;
use App\Models\Activity;
use App\Models\ActivityQuestion;
use App\Models\ActivitySubmission;
use App\Models\ActivitySubmissionAnswer;
use App\Models\User;
use App\Support\ActivitySubmissionGrader;
use App\Support\PerformanceMetricsRebuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function show(Request $request, Activity $activity): Response
    {
        $student = $this->resolveStudent($request);

        $activity->loadMissing([
            'classroom:id,name,code,teacher_id',
            'classroom.teacher:id,name',
            'questions',
            'submissions' => fn ($query) => $query
                ->where('student_id', $student->id)
                ->with('answers'),
        ]);

        abort_unless($this->canAccessActivity($student, $activity), 404);

        $submission = $activity->submissions->first();

        return Inertia::render('student/ActivityShow', [
            'activity' => $this->activityPayload($activity, $submission),
            'submission' => $submission ? $this->submissionPayload($submission) : null,
            'can_submit' => $submission === null,
            'submit_url' => route('student.activities.submissions.store', $activity, absolute: false),
            'back_url' => route('student.classroom', absolute: false),
        ]);
    }

    public function storeSubmission(
        StoreActivitySubmissionRequest $request,
        Activity $activity,
        ActivitySubmissionGrader $grader,
        PerformanceMetricsRebuilder $metricsRebuilder,
    ): RedirectResponse {
        $student = $this->resolveStudent($request);

        $activity->loadMissing([
            'questions',
            'submissions' => fn ($query) => $query->where('student_id', $student->id),
        ]);

        abort_unless($this->canAccessActivity($student, $activity), 404);

        if ($activity->submissions->isNotEmpty()) {
            return to_route('student.activities.show', $activity)
                ->with('error', 'Esta atividade já foi respondida e não pode ser enviada novamente.');
        }

        $gradedSubmission = $grader->grade($activity, $request->validated()['answers']);

        DB::transaction(function () use ($student, $activity, $gradedSubmission): void {
            $submission = ActivitySubmission::query()->create([
                'activity_id' => $activity->id,
                'student_id' => $student->id,
                'classroom_id' => $activity->classroom_id,
                'status' => 'submitted',
                'submitted_at' => now(),
                'score' => $gradedSubmission['score'],
                'total_points' => $gradedSubmission['total_points'],
                'correct_answers_count' => $gradedSubmission['correct_answers_count'],
            ]);

            collect($gradedSubmission['answers'])->each(fn (array $answer) => ActivitySubmissionAnswer::query()->create([
                'activity_submission_id' => $submission->id,
                'activity_question_id' => $answer['question_id'],
                'answer_payload' => $answer['answer_payload'],
                'is_correct' => $answer['is_correct'],
                'earned_points' => $answer['earned_points'],
            ]));
        });

        if ($student->school_id) {
            $metricsRebuilder->rebuildSchool((int) $student->school_id);
        }

        return to_route('student.activities.show', $activity)
            ->with('success', 'Atividade enviada com sucesso. Sua correção já está disponível.');
    }

    private function resolveStudent(Request $request): User
    {
        /** @var User $student */
        $student = $request->user()->loadMissing([
            'role:id,name,label',
            'classrooms:id,name,code,teacher_id',
        ]);

        return $student;
    }

    private function canAccessActivity(User $student, Activity $activity): bool
    {
        $classroom = $student->classrooms->first();

        if (! $classroom) {
            return false;
        }

        return $activity->status === 'published' && $activity->classroom_id === $classroom->id;
    }

    private function activityPayload(Activity $activity, ?ActivitySubmission $submission): array
    {
        $answerResults = $submission?->answers->keyBy('activity_question_id') ?? collect();

        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'total_points' => $activity->total_points,
            'points_per_question' => $activity->points_per_question,
            'questions_count' => $activity->questions_count,
            'due_date' => optional($activity->due_date)?->format('Y-m-d'),
            'due_date_label' => $activity->due_date ? $activity->due_date->format('d/m/Y') : 'Sem prazo',
            'classroom' => [
                'name' => $activity->classroom?->name ?? 'Sala em configuração',
                'code' => $activity->classroom?->code ?? 'Sem código',
            ],
            'teacher' => $activity->classroom?->teacher?->name ?? 'Professor em definição',
            'questions' => $activity->questions
                ->map(function (ActivityQuestion $question) use ($answerResults, $submission) {
                    $answerResult = $answerResults->get($question->id);

                    return [
                        'id' => $question->id,
                        'order' => $question->order,
                        'type' => $question->type,
                        'statement' => $question->statement,
                        'options' => $question->type === 'multiple_choice' ? $question->options : null,
                        'keywords' => $question->type === 'drag_drop' ? $question->keywords : null,
                        'blank_keys' => $question->type === 'drag_drop' ? array_keys($question->blank_answers ?? []) : null,
                        'left_column' => $question->type === 'matching' ? $question->left_column : null,
                        'right_column' => $question->type === 'matching' ? $question->right_column : null,
                        'submitted_answer' => $answerResult?->answer_payload,
                        'is_correct' => $answerResult?->is_correct,
                        'earned_points' => $answerResult?->earned_points,
                        'solution' => $submission ? $this->questionSolution($question) : null,
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    private function submissionPayload(ActivitySubmission $submission): array
    {
        return [
            'id' => $submission->id,
            'status' => $submission->status,
            'submitted_at' => optional($submission->submitted_at)?->toIso8601String(),
            'submitted_at_label' => optional($submission->submitted_at)?->format('d/m/Y H:i'),
            'score' => $submission->score,
            'total_points' => $submission->total_points,
            'correct_answers_count' => $submission->correct_answers_count,
            'answers_count' => $submission->answers->count(),
        ];
    }

    private function questionSolution(ActivityQuestion $question): array
    {
        if ($question->type === 'multiple_choice') {
            return [
                'correct_option' => $question->correct_option,
            ];
        }

        if ($question->type === 'drag_drop') {
            return [
                'blanks' => collect($question->blank_answers ?? [])
                    ->mapWithKeys(fn ($keywordIndex, $blankKey) => [
                        (string) $blankKey => $question->keywords[(int) $keywordIndex] ?? '',
                    ])
                    ->all(),
            ];
        }

        return [
            'pairs' => collect($question->pairs ?? [])
                ->mapWithKeys(fn ($rightIndex, $leftIndex) => [
                    (string) $leftIndex => [
                        'left' => $question->left_column[(int) $leftIndex] ?? '',
                        'right' => $question->right_column[(int) $rightIndex] ?? '',
                    ],
                ])
                ->all(),
        ];
    }
}
