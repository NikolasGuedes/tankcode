<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolDemoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()
            ->whereIn('name', [
                RoleEnum::OWNER->value,
                RoleEnum::DIRECTOR->value,
                RoleEnum::TEACHER->value,
                RoleEnum::STUDENT->value,
            ])
            ->get()
            ->keyBy(fn (Role $role) => $role->name?->value);

        $school = School::query()->updateOrCreate(
            ['cnpj' => '12.345.678/0001-99'],
            [
                'name' => 'Escola Central TankCode',
                'logo_path' => null,
                'status' => 'active',
            ],
        );

        $points = collect([
            [
                'name' => 'Unidade Centro',
                'cnpj' => '22.111.333/0001-40',
                'zip_code' => '74000000',
                'address_line' => 'Rua 10, 245, Centro',
                'status' => 'active',
            ],
            [
                'name' => 'Unidade Norte',
                'cnpj' => '22.111.333/0002-21',
                'zip_code' => '74450010',
                'address_line' => 'Avenida Perimetral, 900, Setor Norte',
                'status' => 'active',
            ],
            [
                'name' => 'Unidade Sul',
                'cnpj' => '22.111.333/0003-02',
                'zip_code' => '74830020',
                'address_line' => 'Rua das Palmeiras, 120, Setor Sul',
                'status' => 'active',
            ],
        ])->map(fn (array $data) => PointOfSchool::query()->updateOrCreate(
            ['school_id' => $school->id, 'cnpj' => preg_replace('/\D+/', '', $data['cnpj'])],
            $data + ['school_id' => $school->id],
        ));

        $owner = $this->createUser(
            roleId: $roles[RoleEnum::OWNER->value]->id,
            schoolId: $school->id,
            name: 'Owner Demo',
            email: 'owner@escola.local',
        );

        $directors = collect([
            ['name' => 'Director Demo', 'email' => 'director@escola.local'],
            ['name' => 'Director Norte', 'email' => 'director.norte@escola.local'],
        ])->map(fn (array $director) => $this->createUser(
            roleId: $roles[RoleEnum::DIRECTOR->value]->id,
            schoolId: $school->id,
            name: $director['name'],
            email: $director['email'],
        ));

        $teachers = collect([
            ['name' => 'Ana Souza', 'email' => 'ana.souza@escola.local'],
            ['name' => 'Bruno Lima', 'email' => 'bruno.lima@escola.local'],
            ['name' => 'Carla Mendes', 'email' => 'carla.mendes@escola.local'],
        ])->map(fn (array $teacher) => $this->createUser(
            roleId: $roles[RoleEnum::TEACHER->value]->id,
            schoolId: $school->id,
            name: $teacher['name'],
            email: $teacher['email'],
        ));

        $students = collect([
            ['name' => 'Julia Martins', 'email' => 'julia.martins@escola.local'],
            ['name' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@escola.local'],
            ['name' => 'Marina Costa', 'email' => 'marina.costa@escola.local'],
            ['name' => 'Pedro Henrique', 'email' => 'pedro.henrique@escola.local'],
            ['name' => 'Laura Alves', 'email' => 'laura.alves@escola.local'],
            ['name' => 'Gustavo Rocha', 'email' => 'gustavo.rocha@escola.local'],
        ])->map(fn (array $student) => $this->createUser(
            roleId: $roles[RoleEnum::STUDENT->value]->id,
            schoolId: $school->id,
            name: $student['name'],
            email: $student['email'],
        ));

        $ownerPointIds = [$points[0]->id, $points[1]->id];
        $directorCentroPointIds = [$points[0]->id];
        $directorNortePointIds = [$points[1]->id];

        $this->syncPoints($owner, $ownerPointIds, RoleEnum::OWNER->label());
        $this->syncPoints($directors[0], $directorCentroPointIds, RoleEnum::DIRECTOR->label());
        $this->syncPoints($directors[1], $directorNortePointIds, RoleEnum::DIRECTOR->label());

        $this->syncPoints($teachers[0], [$points[0]->id], RoleEnum::TEACHER->label());
        $this->syncPoints($teachers[1], [$points[1]->id], RoleEnum::TEACHER->label());
        $this->syncPoints($teachers[2], [$points[2]->id], RoleEnum::TEACHER->label());

        $this->syncPoints($students[0], [$points[0]->id], RoleEnum::STUDENT->label());
        $this->syncPoints($students[1], [$points[0]->id], RoleEnum::STUDENT->label());
        $this->syncPoints($students[2], [$points[1]->id], RoleEnum::STUDENT->label());
        $this->syncPoints($students[3], [$points[1]->id], RoleEnum::STUDENT->label());
        $this->syncPoints($students[4], [$points[2]->id], RoleEnum::STUDENT->label());
        $this->syncPoints($students[5], [$points[2]->id], RoleEnum::STUDENT->label());

        $classrooms = collect([
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[0]->id,
                'teacher_id' => $teachers[0]->id,
                'name' => 'Turma Alfa',
                'code' => 'ALFA-01',
                'status' => 'active',
                'student_ids' => [$students[0]->id, $students[1]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[1]->id,
                'teacher_id' => $teachers[1]->id,
                'name' => 'Turma Beta',
                'code' => 'BETA-01',
                'status' => 'active',
                'student_ids' => [$students[2]->id, $students[3]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[2]->id,
                'teacher_id' => $teachers[2]->id,
                'name' => 'Turma Gama',
                'code' => 'GAMA-01',
                'status' => 'active',
                'student_ids' => [$students[4]->id, $students[5]->id],
            ],
        ])->map(function (array $classroomData) {
            $studentIds = $classroomData['student_ids'];
            unset($classroomData['student_ids']);

            $classroom = Classroom::query()->updateOrCreate(
                ['school_id' => $classroomData['school_id'], 'code' => $classroomData['code']],
                $classroomData,
            );

            $classroom->students()->sync($studentIds);

            return $classroom;
        });

        $directors->each(fn (User $director, int $index) => $director->forceFill([
            'last_login_at' => now()->subDay()->subHours($index),
        ])->save());

        $teachers->each(fn (User $teacher, int $index) => $teacher->forceFill([
            'last_login_at' => now()->subHours($index + 2),
        ])->save());

        $students->each(fn (User $student, int $index) => $student->forceFill([
            'last_login_at' => now()->subMinutes(($index + 1) * 30),
        ])->save());

        $classrooms->each(fn (Classroom $classroom) => $classroom->touch());
    }

    private function createUser(int $roleId, int $schoolId, string $name, string $email): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'role_id' => $roleId,
                'school_id' => $schoolId,
                'name' => $name,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
        );
    }

    private function syncPoints(User $user, array $pointIds, string $title): void
    {
        $payload = collect($pointIds)
            ->values()
            ->mapWithKeys(fn (int $pointId, int $index) => [
                $pointId => [
                    'title' => $title,
                    'is_primary' => $index === 0,
                    'status' => 'active',
                ],
            ])
            ->all();

        $user->pointOfSchools()->sync($payload);
    }
}
