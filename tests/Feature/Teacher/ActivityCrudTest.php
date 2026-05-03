<?php

use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\ActivityQuestion;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\User;

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
