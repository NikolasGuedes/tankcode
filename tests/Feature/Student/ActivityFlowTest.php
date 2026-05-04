<?php

use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\ActivitySubmissionAnswer;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\StudentClassroomPerformance;
use App\Models\User;
use App\Support\PerformanceMetricsRebuilder;
use Inertia\Testing\AssertableInertia as Assert;

function studentActivityScope(): array
{
    $school = School::query()->create([
        'name' => fake()->company(),
        'cnpj' => fake()->unique()->numerify('##############'),
        'status' => 'active',
    ]);

    $point = PointOfSchool::query()->create([
        'school_id' => $school->id,
        'name' => fake()->company(),
        'cnpj' => fake()->unique()->numerify('##############'),
        'address_line' => fake()->streetAddress(),
        'zip_code' => '74000000',
        'status' => 'active',
    ]);

    $teacherRole = Role::query()->firstOrCreate(
        ['name' => RoleEnum::TEACHER->value],
        ['label' => RoleEnum::TEACHER->label()],
    );

    $studentRole = Role::query()->firstOrCreate(
        ['name' => RoleEnum::STUDENT->value],
        ['label' => RoleEnum::STUDENT->label()],
    );

    $teacher = User::factory()->create([
        'role_id' => $teacherRole->id,
        'school_id' => $school->id,
    ]);

    $student = User::factory()->create([
        'role_id' => $studentRole->id,
        'school_id' => $school->id,
    ]);

    $classmate = User::factory()->create([
        'role_id' => $studentRole->id,
        'school_id' => $school->id,
    ]);

    $teacher->pointOfSchools()->attach($point->id, [
        'title' => RoleEnum::TEACHER->label(),
        'is_primary' => true,
        'status' => 'active',
    ]);

    collect([$student, $classmate])->each(fn (User $user) => $user->pointOfSchools()->attach($point->id, [
        'title' => RoleEnum::STUDENT->label(),
        'is_primary' => true,
        'status' => 'active',
    ]));

    $classroom = Classroom::query()->create([
        'school_id' => $school->id,
        'point_of_school_id' => $point->id,
        'teacher_id' => $teacher->id,
        'name' => fake()->words(2, true),
        'code' => fake()->unique()->bothify('TUR-###'),
        'status' => 'active',
    ]);

    $classroom->students()->sync([$student->id, $classmate->id]);

    return [
        'school' => $school,
        'point' => $point,
        'teacher' => $teacher,
        'student' => $student,
        'classmate' => $classmate,
        'classroom' => $classroom,
    ];
}

function studentMultipleChoiceQuestion(array $overrides = []): array
{
    return [
        'type' => 'multiple_choice',
        'statement' => 'Qual alternativa representa uma variavel?',
        'options' => [
            'A' => 'let nome = "Ana"',
            'B' => 'echo',
            'C' => 'select',
            'D' => 'body',
        ],
        'correct_option' => 'A',
        ...$overrides,
    ];
}

function studentDragDropQuestion(array $overrides = []): array
{
    return [
        'type' => 'drag_drop',
        'statement' => 'Complete: HTML define a [blank_1] e CSS define o [blank_2].',
        'keywords' => ['estrutura', 'visual'],
        'blank_answers' => [
            'blank_1' => 0,
            'blank_2' => 1,
        ],
        ...$overrides,
    ];
}

function studentMatchingQuestion(array $overrides = []): array
{
    return [
        'type' => 'matching',
        'statement' => 'Relacione as linguagens com seus usos.',
        'left_column' => ['SQL', 'CSS'],
        'right_column' => ['Banco de dados', 'Estilo visual'],
        'pairs' => [
            '0' => '0',
            '1' => '1',
        ],
        ...$overrides,
    ];
}

function createStudentActivity(Classroom $classroom, User $teacher, array $overrides = [], array $questions = []): Activity
{
    $questions = $questions ?: [studentMultipleChoiceQuestion()];
    $pointsPerQuestion = (float) ($overrides['points_per_question'] ?? 1);

    $activity = Activity::query()->create([
        'classroom_id' => $classroom->id,
        'teacher_id' => $teacher->id,
        'title' => $overrides['title'] ?? 'Atividade do aluno',
        'description' => $overrides['description'] ?? 'Descricao da atividade',
        'level' => $overrides['level'] ?? 'facil',
        'questions_count' => count($questions),
        'points_per_question' => $pointsPerQuestion,
        'total_points' => $overrides['total_points'] ?? (count($questions) * $pointsPerQuestion),
        'due_date' => $overrides['due_date'] ?? now()->addWeek()->toDateString(),
        'status' => $overrides['status'] ?? 'published',
    ]);

    collect($questions)->values()->each(fn (array $question, int $index) => $activity->questions()->create([
        'type' => $question['type'],
        'statement' => $question['statement'],
        'order' => $index + 1,
        'correct_option' => $question['correct_option'] ?? null,
        'options' => $question['options'] ?? null,
        'keywords' => $question['keywords'] ?? null,
        'blank_answers' => $question['blank_answers'] ?? null,
        'left_column' => $question['left_column'] ?? null,
        'right_column' => $question['right_column'] ?? null,
        'pairs' => $question['pairs'] ?? null,
    ]));

    return $activity->fresh('questions');
}

function createSubmissionForStudent(Activity $activity, User $student, array $attributes = []): ActivitySubmission
{
    return ActivitySubmission::query()->create([
        'activity_id' => $activity->id,
        'student_id' => $student->id,
        'classroom_id' => $activity->classroom_id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'score' => $attributes['score'] ?? 1,
        'total_points' => $attributes['total_points'] ?? $activity->total_points,
        'correct_answers_count' => $attributes['correct_answers_count'] ?? 1,
    ]);
}

test('aluno ve apenas atividades publicadas da propria turma', function () {
    ['teacher' => $teacher, 'student' => $student, 'classroom' => $classroom] = studentActivityScope();
    ['teacher' => $otherTeacher, 'classroom' => $otherClassroom] = studentActivityScope();

    createStudentActivity($classroom, $teacher, ['title' => 'Atividade publicada']);
    createStudentActivity($classroom, $teacher, ['title' => 'Rascunho oculto', 'status' => 'draft']);
    createStudentActivity($otherClassroom, $otherTeacher, ['title' => 'Outra turma']);

    $this->actingAs($student)
        ->get(route('student.classroom'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('student/Classroom')
            ->has('activities', 1)
            ->where('activities.0.title', 'Atividade publicada')
            ->where('activities.0.href', route('student.activities.show', Activity::query()->where('title', 'Atividade publicada')->firstOrFail(), absolute: false))
        );
});

test('aluno nao acessa atividade de outra turma', function () {
    ['student' => $student] = studentActivityScope();
    ['teacher' => $otherTeacher, 'classroom' => $otherClassroom] = studentActivityScope();
    $activity = createStudentActivity($otherClassroom, $otherTeacher);

    $this->actingAs($student)
        ->get(route('student.activities.show', $activity))
        ->assertNotFound();
});

test('aluno consegue enviar atividade com os tres tipos de questao', function () {
    ['teacher' => $teacher, 'student' => $student, 'classroom' => $classroom] = studentActivityScope();

    $activity = createStudentActivity($classroom, $teacher, [
        'level' => 'media',
        'points_per_question' => 2.5,
        'total_points' => 7.5,
    ], [
        studentMultipleChoiceQuestion(),
        studentDragDropQuestion(),
        studentMatchingQuestion(),
    ]);

    $payload = [
        'answers' => [
            [
                'question_id' => $activity->questions[0]->id,
                'selected_option' => 'A',
            ],
            [
                'question_id' => $activity->questions[1]->id,
                'blanks' => [
                    'blank_1' => '0',
                    'blank_2' => '1',
                ],
            ],
            [
                'question_id' => $activity->questions[2]->id,
                'pairs' => [
                    '0' => '0',
                    '1' => '1',
                ],
            ],
        ],
    ];

    $this->actingAs($student)
        ->post(route('student.activities.submissions.store', $activity), $payload)
        ->assertRedirect(route('student.activities.show', $activity));

    $submission = ActivitySubmission::query()->where('activity_id', $activity->id)->where('student_id', $student->id)->firstOrFail();

    expect((float) $submission->score)->toBe(7.5)
        ->and((int) $submission->correct_answers_count)->toBe(3)
        ->and(ActivitySubmissionAnswer::query()->where('activity_submission_id', $submission->id)->count())->toBe(3);
});

test('correcao calcula score e pontos por questao corretamente', function () {
    ['teacher' => $teacher, 'student' => $student, 'classroom' => $classroom] = studentActivityScope();

    $activity = createStudentActivity($classroom, $teacher, [
        'level' => 'media',
        'points_per_question' => 2.5,
        'total_points' => 7.5,
    ], [
        studentMultipleChoiceQuestion(),
        studentDragDropQuestion(),
        studentMatchingQuestion(),
    ]);

    $this->actingAs($student)->post(route('student.activities.submissions.store', $activity), [
        'answers' => [
            [
                'question_id' => $activity->questions[0]->id,
                'selected_option' => 'A',
            ],
            [
                'question_id' => $activity->questions[1]->id,
                'blanks' => [
                    'blank_1' => '1',
                    'blank_2' => '0',
                ],
            ],
            [
                'question_id' => $activity->questions[2]->id,
                'pairs' => [
                    '0' => '1',
                    '1' => '0',
                ],
            ],
        ],
    ]);

    $submission = ActivitySubmission::query()->where('activity_id', $activity->id)->where('student_id', $student->id)->firstOrFail();
    $answers = ActivitySubmissionAnswer::query()->where('activity_submission_id', $submission->id)->orderBy('activity_question_id')->get();

    expect((float) $submission->score)->toBe(2.5)
        ->and((int) $submission->correct_answers_count)->toBe(1)
        ->and((float) $answers[0]->earned_points)->toBe(2.5)
        ->and((float) $answers[1]->earned_points)->toBe(0.0)
        ->and((float) $answers[2]->earned_points)->toBe(0.0);
});

test('segunda tentativa e bloqueada', function () {
    ['teacher' => $teacher, 'student' => $student, 'classroom' => $classroom] = studentActivityScope();
    $activity = createStudentActivity($classroom, $teacher);

    $payload = [
        'answers' => [
            [
                'question_id' => $activity->questions[0]->id,
                'selected_option' => 'A',
            ],
        ],
    ];

    $this->actingAs($student)->post(route('student.activities.submissions.store', $activity), $payload);

    $this->actingAs($student)
        ->post(route('student.activities.submissions.store', $activity), $payload)
        ->assertRedirect(route('student.activities.show', $activity));

    expect(ActivitySubmission::query()->where('activity_id', $activity->id)->where('student_id', $student->id)->count())->toBe(1);
});

test('atividade respondida abre em modo somente leitura', function () {
    ['teacher' => $teacher, 'student' => $student, 'classroom' => $classroom] = studentActivityScope();
    $activity = createStudentActivity($classroom, $teacher, [], [studentMultipleChoiceQuestion()]);
    $submission = createSubmissionForStudent($activity, $student, [
        'score' => 1,
        'correct_answers_count' => 1,
    ]);

    ActivitySubmissionAnswer::query()->create([
        'activity_submission_id' => $submission->id,
        'activity_question_id' => $activity->questions[0]->id,
        'answer_payload' => [
            'selected_option' => 'A',
        ],
        'is_correct' => true,
        'earned_points' => 1,
    ]);

    $this->actingAs($student)
        ->get(route('student.activities.show', $activity))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('student/ActivityShow')
            ->where('can_submit', false)
            ->where('submission.correct_answers_count', 1)
            ->where('activity.questions.0.submitted_answer.selected_option', 'A')
        );
});

test('score e ranking do aluno atualizam logo apos o envio', function () {
    ['teacher' => $teacher, 'student' => $student, 'classmate' => $classmate, 'classroom' => $classroom, 'school' => $school] = studentActivityScope();

    $activity = createStudentActivity($classroom, $teacher);
    createSubmissionForStudent($activity, $classmate, [
        'score' => 0,
        'correct_answers_count' => 0,
    ]);

    app(PerformanceMetricsRebuilder::class)->rebuildSchool($school->id);

    $this->actingAs($student)
        ->post(route('student.activities.submissions.store', $activity), [
            'answers' => [
                [
                    'question_id' => $activity->questions[0]->id,
                    'selected_option' => 'A',
                ],
            ],
        ])
        ->assertRedirect(route('student.activities.show', $activity));

    $performance = StudentClassroomPerformance::query()
        ->where('student_id', $student->id)
        ->where('classroom_id', $classroom->id)
        ->firstOrFail();

    expect((float) $performance->total_score)->toBe(1.0)
        ->and((int) $performance->submitted_activities_count)->toBe(1)
        ->and((int) $performance->classroom_rank)->toBe(1)
        ->and((int) $performance->school_rank)->toBe(1);

    $this->actingAs($student)
        ->get(route('student.classroom'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('student/Classroom')
            ->where('score.student_points', 1.0)
            ->where('score.classroom_rank', 1)
            ->where('classmates.0.name', $student->name)
            ->where('classmates.0.ranking_position', 1)
        );
});

test('perfil e score global usam agregados reais do aluno', function () {
    ['teacher' => $teacher, 'student' => $student, 'classmate' => $classmate, 'classroom' => $classroom, 'school' => $school] = studentActivityScope();

    $activity = createStudentActivity($classroom, $teacher);
    createSubmissionForStudent($activity, $classmate, [
        'score' => 0,
        'correct_answers_count' => 0,
    ]);

    app(PerformanceMetricsRebuilder::class)->rebuildSchool($school->id);

    $this->actingAs($student)->post(route('student.activities.submissions.store', $activity), [
        'answers' => [
            [
                'question_id' => $activity->questions[0]->id,
                'selected_option' => 'A',
            ],
        ],
    ]);

    $this->actingAs($student)
        ->get(route('student.profile'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('student/Profile')
            ->where('profile.points', 1.0)
            ->where('profile.status.completed_activities', 1)
            ->where('profile.status.ranking_position', 1)
        );

    $this->actingAs($student)
        ->get(route('student.score-global'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('student/ScoreGlobal')
            ->where('score.student_points', 1.0)
            ->where('score.classroom_rank', 1)
            ->where('score.point_rank', 1)
            ->where('score.school_rank', 1)
            ->where('students.0.name', $student->name)
            ->where('students.0.score', 1.0)
        );
});
