<?php

use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\ActivityQuestion;
use App\Models\ActivitySubmission;
use App\Models\ActivitySubmissionAnswer;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\StudentClassroomPerformance;
use App\Models\TeacherClassroomMetric;
use App\Models\TeacherMonthlyMetric;
use App\Models\User;
use App\Support\PerformanceMetricsRebuilder;
use Illuminate\Support\Facades\Artisan;
use Inertia\Testing\AssertableInertia as Assert;

function teacherActivityScope(): array
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

    $teacher = User::factory()->create([
        'role_id' => $teacherRole->id,
        'school_id' => $school->id,
    ]);

    $teacher->pointOfSchools()->attach($point->id, [
        'title' => RoleEnum::TEACHER->label(),
        'is_primary' => true,
        'status' => 'active',
    ]);

    $classroom = Classroom::query()->create([
        'school_id' => $school->id,
        'point_of_school_id' => $point->id,
        'teacher_id' => $teacher->id,
        'name' => fake()->words(2, true),
        'code' => fake()->unique()->bothify('TUR-###'),
        'status' => 'active',
    ]);

    return [
        'school' => $school,
        'point' => $point,
        'teacher' => $teacher,
        'classroom' => $classroom,
    ];
}

function multipleChoiceQuestion(array $overrides = []): array
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

function dragDropQuestion(array $overrides = []): array
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

function matchingQuestion(array $overrides = []): array
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

function validActivityPayload(array $overrides = []): array
{
    return [
        'title' => 'Atividade de Logica',
        'description' => 'Lista avaliativa',
        'level' => 'media',
        'classroom_id' => $overrides['classroom_id'] ?? null,
        'due_date' => now()->addWeek()->format('Y-m-d'),
        'status' => 'draft',
        'questions' => [multipleChoiceQuestion()],
        'questions_count' => 999,
        'points_per_question' => 999,
        'total_points' => 999,
        ...$overrides,
    ];
}

function createActivityForTeacher(User $teacher, Classroom $classroom, array $questions = []): Activity
{
    $questions = $questions ?: [multipleChoiceQuestion()];

    $activity = Activity::query()->create([
        'classroom_id' => $classroom->id,
        'teacher_id' => $teacher->id,
        'title' => 'Atividade antiga',
        'level' => 'facil',
        'questions_count' => count($questions),
        'points_per_question' => 1,
        'total_points' => count($questions),
        'status' => 'draft',
    ]);

    collect($questions)->each(fn (array $question, int $index) => $activity->questions()->create([
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

    return $activity;
}

function attachStudentToTeacherClassroom(School $school, PointOfSchool $point, Classroom $classroom, string $name, string $email): User
{
    $studentRole = Role::query()->firstOrCreate(
        ['name' => RoleEnum::STUDENT->value],
        ['label' => RoleEnum::STUDENT->label()],
    );

    $student = User::factory()->create([
        'role_id' => $studentRole->id,
        'school_id' => $school->id,
        'name' => $name,
        'email' => $email,
    ]);

    $student->pointOfSchools()->attach($point->id, [
        'title' => RoleEnum::STUDENT->label(),
        'is_primary' => true,
        'status' => 'active',
    ]);

    $classroom->students()->syncWithoutDetaching([$student->id]);

    return $student;
}

test('cria atividade com multipla escolha e calcula pontos corretamente', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)->post(route('teacher.activities.store'), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'facil',
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity = Activity::query()->with('questions')->firstOrFail();

    expect($activity->questions_count)->toBe(1)
        ->and((float) $activity->points_per_question)->toBe(1.0)
        ->and((float) $activity->total_points)->toBe(1.0)
        ->and($activity->questions)->toHaveCount(1)
        ->and($activity->questions->first()->type)->toBe('multiple_choice')
        ->and($activity->questions->first()->options['A'])->toBe('let nome = "Ana"');
});

test('cria atividade com mais de uma questao e usa a quantidade real no calculo', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)->post(route('teacher.activities.store'), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'media',
        'questions' => [
            multipleChoiceQuestion(),
            dragDropQuestion(),
            matchingQuestion(),
        ],
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity = Activity::query()->with('questions')->firstOrFail();

    expect($activity->questions_count)->toBe(3)
        ->and((float) $activity->points_per_question)->toBe(2.5)
        ->and((float) $activity->total_points)->toBe(7.5)
        ->and($activity->questions)->toHaveCount(3)
        ->and($activity->questions->pluck('type')->all())->toBe(['multiple_choice', 'drag_drop', 'matching']);
});

test('update substitui questoes e recalcula questions_count e total_points', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();
    $activity = createActivityForTeacher($teacher, $classroom);

    $this->actingAs($teacher)->put(route('teacher.activities.update', $activity), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'dificil',
        'questions' => [
            multipleChoiceQuestion(['statement' => 'Questao atualizada']),
            matchingQuestion(),
        ],
        'questions_count' => 50,
        'points_per_question' => 1,
        'total_points' => 1,
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity->refresh()->load('questions');

    expect($activity->questions_count)->toBe(2)
        ->and((float) $activity->points_per_question)->toBe(4.0)
        ->and((float) $activity->total_points)->toBe(8.0)
        ->and($activity->questions)->toHaveCount(2)
        ->and($activity->questions->first()->statement)->toBe('Questao atualizada');
});

test('validacao impede criar atividade sem questoes', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)
        ->from(route('teacher.activities.index'))
        ->post(route('teacher.activities.store'), validActivityPayload([
            'classroom_id' => $classroom->id,
            'questions' => [],
        ]))
        ->assertRedirect(route('teacher.activities.index'))
        ->assertSessionHasErrors('questions');

    expect(Activity::query()->count())->toBe(0)
        ->and(ActivityQuestion::query()->count())->toBe(0);
});

test('validacao impede tipo de questao invalido', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)
        ->from(route('teacher.activities.index'))
        ->post(route('teacher.activities.store'), validActivityPayload([
            'classroom_id' => $classroom->id,
            'questions' => [
                multipleChoiceQuestion(['type' => 'invalida']),
            ],
        ]))
        ->assertRedirect(route('teacher.activities.index'))
        ->assertSessionHasErrors('questions.0.type');

    expect(Activity::query()->count())->toBe(0)
        ->and(ActivityQuestion::query()->count())->toBe(0);
});

test('validacao bloqueia nivel invalido', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)
        ->from(route('teacher.activities.index'))
        ->post(route('teacher.activities.store'), validActivityPayload([
            'classroom_id' => $classroom->id,
            'level' => 'impossivel',
        ]))
        ->assertRedirect(route('teacher.activities.index'))
        ->assertSessionHasErrors('level');

    expect(Activity::query()->count())->toBe(0);
});

test('professor nao consegue criar atividade em turma fora do seu escopo', function () {
    ['teacher' => $teacher] = teacherActivityScope();
    ['classroom' => $otherClassroom] = teacherActivityScope();

    $this->actingAs($teacher)
        ->from(route('teacher.activities.index'))
        ->post(route('teacher.activities.store'), validActivityPayload([
            'classroom_id' => $otherClassroom->id,
        ]))
        ->assertRedirect(route('teacher.activities.index'))
        ->assertSessionHasErrors('classroom_id');

    expect(Activity::query()->count())->toBe(0);
});

test('professor nao consegue editar ou excluir atividade de outro professor fora do escopo', function () {
    ['teacher' => $teacher] = teacherActivityScope();
    ['teacher' => $otherTeacher, 'classroom' => $otherClassroom] = teacherActivityScope();
    $activity = createActivityForTeacher($otherTeacher, $otherClassroom);

    $this->actingAs($teacher)
        ->put(route('teacher.activities.update', $activity), validActivityPayload([
            'classroom_id' => $otherClassroom->id,
            'title' => 'Tentativa de edicao',
        ]))
        ->assertForbidden();

    $this->actingAs($teacher)
        ->delete(route('teacher.activities.destroy', $activity))
        ->assertNotFound();

    $activity->refresh();

    expect($activity->title)->toBe('Atividade antiga');
});

test('dashboard do professor exibe metricas reais apos submissao de aluno', function () {
    ['teacher' => $teacher, 'classroom' => $classroom, 'school' => $school, 'point' => $point] = teacherActivityScope();

    $firstStudent = attachStudentToTeacherClassroom($school, $point, $classroom, 'Aluno Um', 'aluno1@escola.local');
    attachStudentToTeacherClassroom($school, $point, $classroom, 'Aluno Dois', 'aluno2@escola.local');

    $activity = createActivityForTeacher($teacher, $classroom);
    $activity->update([
        'status' => 'published',
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    $submission = ActivitySubmission::query()->create([
        'activity_id' => $activity->id,
        'student_id' => $firstStudent->id,
        'classroom_id' => $classroom->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'score' => 1,
        'total_points' => 1,
        'correct_answers_count' => 1,
    ]);

    ActivitySubmissionAnswer::query()->create([
        'activity_submission_id' => $submission->id,
        'activity_question_id' => $activity->questions()->firstOrFail()->id,
        'answer_payload' => ['selected_option' => 'A'],
        'is_correct' => true,
        'earned_points' => 1,
    ]);

    app(PerformanceMetricsRebuilder::class)->rebuildSchool($school->id);

    $this->actingAs($teacher)
        ->get(route('teacher.metrics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teacher/Dashboard')
            ->where('performanceMetrics.submittedActivities', 1)
            ->where('performanceMetrics.pendingSubmissions', 1)
            ->where('performanceMetrics.averageCompletionRate', 50.0)
            ->where('performanceMetrics.averagePerformance', 100.0)
            ->where('performanceMetrics.topStudent', 'Aluno Um')
            ->where('performanceMetrics.topClassroom', $classroom->name)
            ->where('classroomSummary.0.submitted', 1)
            ->where('classroomSummary.0.pending', 1)
        );
});

test('criacao de atividade publicada atualiza metricas agregadas do professor', function () {
    ['teacher' => $teacher, 'classroom' => $classroom, 'school' => $school, 'point' => $point] = teacherActivityScope();

    attachStudentToTeacherClassroom($school, $point, $classroom, 'Aluno Um', 'aluno3@escola.local');
    attachStudentToTeacherClassroom($school, $point, $classroom, 'Aluno Dois', 'aluno4@escola.local');

    $this->actingAs($teacher)->post(route('teacher.activities.store'), validActivityPayload([
        'classroom_id' => $classroom->id,
        'status' => 'published',
    ]))->assertRedirect(route('teacher.activities.index'));

    $metric = TeacherClassroomMetric::query()
        ->where('teacher_id', $teacher->id)
        ->where('classroom_id', $classroom->id)
        ->firstOrFail();

    expect((int) $metric->published_activities_count)->toBe(1)
        ->and((int) $metric->expected_submissions_count)->toBe(2)
        ->and((int) $metric->pending_submissions_count)->toBe(2);
});

test('comando de recalculo reconstrói scores e metricas derivadas', function () {
    ['teacher' => $teacher, 'classroom' => $classroom, 'school' => $school, 'point' => $point] = teacherActivityScope();

    $student = attachStudentToTeacherClassroom($school, $point, $classroom, 'Aluno Tres', 'aluno5@escola.local');
    $activity = createActivityForTeacher($teacher, $classroom);
    $activity->update([
        'status' => 'published',
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    $submission = ActivitySubmission::query()->create([
        'activity_id' => $activity->id,
        'student_id' => $student->id,
        'classroom_id' => $classroom->id,
        'status' => 'submitted',
        'submitted_at' => now(),
        'score' => 1,
        'total_points' => 1,
        'correct_answers_count' => 1,
    ]);

    ActivitySubmissionAnswer::query()->create([
        'activity_submission_id' => $submission->id,
        'activity_question_id' => $activity->questions()->firstOrFail()->id,
        'answer_payload' => ['selected_option' => 'A'],
        'is_correct' => true,
        'earned_points' => 1,
    ]);

    expect(StudentClassroomPerformance::query()->count())->toBe(0)
        ->and(TeacherClassroomMetric::query()->count())->toBe(0)
        ->and(TeacherMonthlyMetric::query()->count())->toBe(0);

    Artisan::call('metrics:recalculate', ['--school_id' => $school->id]);

    $performance = StudentClassroomPerformance::query()
        ->where('student_id', $student->id)
        ->where('classroom_id', $classroom->id)
        ->firstOrFail();
    $metric = TeacherClassroomMetric::query()
        ->where('teacher_id', $teacher->id)
        ->where('classroom_id', $classroom->id)
        ->firstOrFail();

    expect((float) $performance->total_score)->toBe(1.0)
        ->and((int) $performance->submitted_activities_count)->toBe(1)
        ->and((int) $metric->submitted_submissions_count)->toBe(1)
        ->and(TeacherMonthlyMetric::query()->where('teacher_id', $teacher->id)->exists())->toBeTrue();
});
