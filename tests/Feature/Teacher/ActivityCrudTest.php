<?php

use App\Enums\RoleEnum;
use App\Models\Activity;
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

function validActivityPayload(array $overrides = []): array
{
    return [
        'title' => 'Atividade de Logica',
        'description' => 'Lista avaliativa',
        'level' => 'media',
        'questions_count' => 4,
        'classroom_id' => $overrides['classroom_id'] ?? null,
        'due_date' => now()->addWeek()->format('Y-m-d'),
        'status' => 'draft',
        'points_per_question' => 999,
        'total_points' => 999,
        ...$overrides,
    ];
}

test('criacao de atividade facil calcula um ponto por questao', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)->post(route('teacher.activities.store'), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'facil',
        'questions_count' => 6,
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity = Activity::query()->firstOrFail();

    expect((float) $activity->points_per_question)->toBe(1.0)
        ->and((float) $activity->total_points)->toBe(6.0);
});

test('criacao de atividade media calcula dois e meio pontos por questao', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)->post(route('teacher.activities.store'), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'media',
        'questions_count' => 8,
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity = Activity::query()->firstOrFail();

    expect((float) $activity->points_per_question)->toBe(2.5)
        ->and((float) $activity->total_points)->toBe(20.0);
});

test('criacao de atividade dificil calcula quatro pontos por questao', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)->post(route('teacher.activities.store'), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'dificil',
        'questions_count' => 5,
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity = Activity::query()->firstOrFail();

    expect((float) $activity->points_per_question)->toBe(4.0)
        ->and((float) $activity->total_points)->toBe(20.0);
});

test('update recalcula pontos ao alterar nivel e quantidade de questoes', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $activity = Activity::query()->create([
        'classroom_id' => $classroom->id,
        'teacher_id' => $teacher->id,
        'title' => 'Atividade antiga',
        'level' => 'facil',
        'questions_count' => 3,
        'points_per_question' => 1,
        'total_points' => 3,
        'status' => 'draft',
    ]);

    $this->actingAs($teacher)->put(route('teacher.activities.update', $activity), validActivityPayload([
        'classroom_id' => $classroom->id,
        'level' => 'dificil',
        'questions_count' => 7,
        'points_per_question' => 1,
        'total_points' => 1,
    ]))->assertRedirect(route('teacher.activities.index'));

    $activity->refresh();

    expect((float) $activity->points_per_question)->toBe(4.0)
        ->and((float) $activity->total_points)->toBe(28.0);
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

test('validacao bloqueia quantidade de questoes menor que um', function () {
    ['teacher' => $teacher, 'classroom' => $classroom] = teacherActivityScope();

    $this->actingAs($teacher)
        ->from(route('teacher.activities.index'))
        ->post(route('teacher.activities.store'), validActivityPayload([
            'classroom_id' => $classroom->id,
            'questions_count' => 0,
        ]))
        ->assertRedirect(route('teacher.activities.index'))
        ->assertSessionHasErrors('questions_count');

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

    $activity = Activity::query()->create([
        'classroom_id' => $otherClassroom->id,
        'teacher_id' => $otherTeacher->id,
        'title' => 'Atividade privada',
        'level' => 'media',
        'questions_count' => 4,
        'points_per_question' => 2.5,
        'total_points' => 10,
        'status' => 'draft',
    ]);

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

    expect($activity->title)->toBe('Atividade privada');
});
