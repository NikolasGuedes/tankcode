<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\ActivitySubmissionAnswer;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\StudentAchievement;
use App\Models\User;
use App\Support\PerformanceMetricsRebuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolDemoSeeder extends Seeder
{
    public function run(): void
    {
        $schoolCnpj = $this->normalizeDigits('12.345.678/0001-99');

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
            ['cnpj' => $schoolCnpj],
            [
                'name' => 'USCS',
                'cnpj' => $schoolCnpj,
                'logo_path' => null,
                'status' => 'active',
            ],
        );

        $points = collect([
            [
                'name' => 'CENTRO',
                'cnpj' => '22.111.333/0001-40',
                'zip_code' => '09510000',
                'address_line' => 'Rua Manoel Coelho, 600, Centro, São Caetano do Sul - SP',
                'status' => 'active',
            ],
            [
                'name' => 'BARCELONA',
                'cnpj' => '22.111.333/0002-21',
                'zip_code' => '09560010',
                'address_line' => 'Rua Alegre, 410, Barcelona, São Caetano do Sul - SP',
                'status' => 'active',
            ],
            [
                'name' => 'CONCEIÇÃO',
                'cnpj' => '22.111.333/0003-02',
                'zip_code' => '09580020',
                'address_line' => 'Avenida Goiás, 1350, Conceição, São Caetano do Sul - SP',
                'status' => 'active',
            ],
        ])->map(function (array $data) use ($school) {
            $normalizedCnpj = $this->normalizeDigits($data['cnpj']);

            return PointOfSchool::query()->updateOrCreate(
                ['cnpj' => $normalizedCnpj],
                array_merge($data, [
                    'school_id' => $school->id,
                    'cnpj' => $normalizedCnpj,
                ]),
            );
        });

        $owner = $this->createUser(
            roleId: $roles[RoleEnum::OWNER->value]->id,
            schoolId: $school->id,
            name: 'Direcao USCS',
            email: 'direcao@uscs.local',
        );

        $directors = collect([
            ['name' => 'Coordenacao Academica', 'email' => 'coordenacao@uscs.local'],
            ['name' => 'Gestao Pedagogica', 'email' => 'gestao.pedagogica@uscs.local'],
        ])->map(fn (array $director) => $this->createUser(
            roleId: $roles[RoleEnum::DIRECTOR->value]->id,
            schoolId: $school->id,
            name: $director['name'],
            email: $director['email'],
        ));

        $teachers = collect([
            ['name' => 'Ana Paula Ribeiro', 'email' => 'ana.ribeiro@uscs.local'],
            ['name' => 'Bruno Cardoso', 'email' => 'bruno.cardoso@uscs.local'],
            ['name' => 'Carla Menezes', 'email' => 'carla.menezes@uscs.local'],
            ['name' => 'Daniel Freitas', 'email' => 'daniel.freitas@uscs.local'],
            ['name' => 'Elisa Barros', 'email' => 'elisa.barros@uscs.local'],
        ])->map(fn (array $teacher) => $this->createUser(
            roleId: $roles[RoleEnum::TEACHER->value]->id,
            schoolId: $school->id,
            name: $teacher['name'],
            email: $teacher['email'],
        ));

        $students = collect([
            ['name' => 'Julia Souza', 'email' => 'julia.souza@uscs.local'],
            ['name' => 'Lucas Pires', 'email' => 'lucas.pires@uscs.local'],
            ['name' => 'Marina Oliveira', 'email' => 'marina.oliveira@uscs.local'],
            ['name' => 'Pedro Henrique', 'email' => 'pedro.henrique@uscs.local'],
            ['name' => 'Laura Martins', 'email' => 'laura.martins@uscs.local'],
            ['name' => 'Gustavo Alves', 'email' => 'gustavo.alves@uscs.local'],
            ['name' => 'Beatriz Gomes', 'email' => 'beatriz.gomes@uscs.local'],
            ['name' => 'Renato Silva', 'email' => 'renato.silva@uscs.local'],
            ['name' => 'Sophia Costa', 'email' => 'sophia.costa@uscs.local'],
            ['name' => 'Miguel Ferreira', 'email' => 'miguel.ferreira@uscs.local'],
            ['name' => 'Isabela Nunes', 'email' => 'isabela.nunes@uscs.local'],
            ['name' => 'Henrique Melo', 'email' => 'henrique.melo@uscs.local'],
            ['name' => 'Helena Castro', 'email' => 'helena.castro@uscs.local'],
            ['name' => 'Tiago Ramos', 'email' => 'tiago.ramos@uscs.local'],
            ['name' => 'Camila Azevedo', 'email' => 'camila.azevedo@uscs.local'],
            ['name' => 'Nicolas Pereira', 'email' => 'nicolas.pereira@uscs.local'],
            ['name' => 'Aline Duarte', 'email' => 'aline.duarte@uscs.local'],
            ['name' => 'Felipe Martins', 'email' => 'felipe.martins@uscs.local'],
            ['name' => 'Raquel Lima', 'email' => 'raquel.lima@uscs.local'],
            ['name' => 'Bruna Rocha', 'email' => 'bruna.rocha@uscs.local'],
            ['name' => 'Caio Nogueira', 'email' => 'caio.nogueira@uscs.local'],
            ['name' => 'Larissa Campos', 'email' => 'larissa.campos@uscs.local'],
            ['name' => 'Otavio Mendes', 'email' => 'otavio.mendes@uscs.local'],
            ['name' => 'Manuela Pires', 'email' => 'manuela.pires@uscs.local'],
            ['name' => 'Davi Costa', 'email' => 'davi.costa@uscs.local'],
            ['name' => 'Elisa Rocha', 'email' => 'elisa.rocha@uscs.local'],
            ['name' => 'Joao Victor', 'email' => 'joao.victor@uscs.local'],
            ['name' => 'Leticia Barros', 'email' => 'leticia.barros@uscs.local'],
            ['name' => 'Arthur Lima', 'email' => 'arthur.lima@uscs.local'],
            ['name' => 'Sofia Almeida', 'email' => 'sofia.almeida@uscs.local'],
            ['name' => 'Bruno Santos', 'email' => 'bruno.santos@uscs.local'],
            ['name' => 'Maria Clara', 'email' => 'maria.clara@uscs.local'],
            ['name' => 'Yago Fernandes', 'email' => 'yago.fernandes@uscs.local'],
            ['name' => 'Amanda Ribeiro', 'email' => 'amanda.ribeiro@uscs.local'],
            ['name' => 'Igor Moreira', 'email' => 'igor.moreira@uscs.local'],
            ['name' => 'Nina Carvalho', 'email' => 'nina.carvalho@uscs.local'],
            ['name' => 'Pedro Lucas', 'email' => 'pedro.lucas@uscs.local'],
            ['name' => 'Rafaela Dias', 'email' => 'rafaela.dias@uscs.local'],
            ['name' => 'Kevin Souza', 'email' => 'kevin.souza@uscs.local'],
            ['name' => 'Bianca Teixeira', 'email' => 'bianca.teixeira@uscs.local'],
            ['name' => 'Enzo Martins', 'email' => 'enzo.martins@uscs.local'],
            ['name' => 'Camila Nascimento', 'email' => 'camila.nascimento@uscs.local'],
        ])->map(fn (array $student) => $this->createUser(
            roleId: $roles[RoleEnum::STUDENT->value]->id,
            schoolId: $school->id,
            name: $student['name'],
            email: $student['email'],
        ));

        $this->syncPoints($owner, [$points[0]->id, $points[1]->id, $points[2]->id], RoleEnum::OWNER->label());
        $this->syncPoints($directors[0], [$points[0]->id, $points[1]->id, $points[2]->id], RoleEnum::DIRECTOR->label());
        $this->syncPoints($directors[1], [$points[1]->id, $points[2]->id], RoleEnum::DIRECTOR->label());

        $this->syncPoints($teachers[0], [$points[0]->id, $points[1]->id], RoleEnum::TEACHER->label());
        $this->syncPoints($teachers[1], [$points[1]->id, $points[2]->id], RoleEnum::TEACHER->label());
        $this->syncPoints($teachers[2], [$points[0]->id], RoleEnum::TEACHER->label());
        $this->syncPoints($teachers[3], [$points[0]->id, $points[2]->id], RoleEnum::TEACHER->label());
        $this->syncPoints($teachers[4], [$points[1]->id], RoleEnum::TEACHER->label());

        $classrooms = collect([
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[0]->id,
                'teacher_id' => $teachers[0]->id,
                'name' => 'Fundamentos de Desenvolvimento',
                'code' => 'FED-01',
                'status' => 'active',
                'student_ids' => [$students[0]->id, $students[1]->id, $students[14]->id, $students[15]->id, $students[16]->id, $students[17]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[1]->id,
                'teacher_id' => $teachers[0]->id,
                'name' => 'Lógica e Algoritmos',
                'code' => 'LOG-01',
                'status' => 'active',
                'student_ids' => [$students[2]->id, $students[3]->id, $students[18]->id, $students[19]->id, $students[20]->id, $students[21]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[2]->id,
                'teacher_id' => $teachers[1]->id,
                'name' => 'Banco de Dados',
                'code' => 'DAD-01',
                'status' => 'active',
                'student_ids' => [$students[4]->id, $students[5]->id, $students[22]->id, $students[23]->id, $students[24]->id, $students[25]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[1]->id,
                'teacher_id' => $teachers[1]->id,
                'name' => 'Interface e Experiência',
                'code' => 'UIX-01',
                'status' => 'active',
                'student_ids' => [$students[6]->id, $students[7]->id, $students[26]->id, $students[27]->id, $students[28]->id, $students[29]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[0]->id,
                'teacher_id' => $teachers[2]->id,
                'name' => 'Aplicativos Mobile',
                'code' => 'APP-01',
                'status' => 'active',
                'student_ids' => [$students[8]->id, $students[9]->id, $students[30]->id, $students[31]->id, $students[32]->id, $students[33]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[2]->id,
                'teacher_id' => $teachers[3]->id,
                'name' => 'Laboratório Integrador',
                'code' => 'LAB-01',
                'status' => 'active',
                'student_ids' => [$students[10]->id, $students[11]->id, $students[34]->id, $students[35]->id, $students[36]->id, $students[37]->id],
            ],
            [
                'school_id' => $school->id,
                'point_of_school_id' => $points[1]->id,
                'teacher_id' => $teachers[4]->id,
                'name' => 'Projeto Orientado',
                'code' => 'ORI-01',
                'status' => 'active',
                'student_ids' => [$students[12]->id, $students[13]->id, $students[38]->id, $students[39]->id, $students[40]->id, $students[41]->id],
            ],
        ])->map(function (array $classroomData) {
            $studentIds = $classroomData['student_ids'];
            unset($classroomData['student_ids']);

            $classroom = Classroom::query()->updateOrCreate(
                ['school_id' => $classroomData['school_id'], 'code' => $classroomData['code']],
                $classroomData,
            );

            $classroom->students()->sync($studentIds);

            $studentPayload = collect($studentIds)
                ->mapWithKeys(fn (int $studentId) => [
                    $studentId => [
                        'title' => RoleEnum::STUDENT->label(),
                        'is_primary' => true,
                        'status' => 'active',
                    ],
                ])
                ->all();

            User::query()
                ->whereIn('id', $studentIds)
                ->get()
                ->each(fn (User $student) => $student->pointOfSchools()->sync([
                    $classroomData['point_of_school_id'] => [
                        'title' => RoleEnum::STUDENT->label(),
                        'is_primary' => true,
                        'status' => 'active',
                    ],
                ]));

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

        $this->seedStudentProfiles($students);
        $classrooms->each(fn (Classroom $classroom) => $classroom->touch());
        $this->seedActivities($classrooms, $students);
        StudentAchievement::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->delete();

        app(PerformanceMetricsRebuilder::class)->rebuildSchool($school->id);
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

    private function normalizeDigits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? $value;
    }

    private function seedActivities($classrooms, $students): void
    {
        $activityBlueprints = [
            'FED-01' => [
                [
                    'activity' => [
                        'title' => 'Boas-vindas USCS',
                        'description' => 'Introducao ao ambiente academico e digital da USCS.',
                        'level' => 'facil',
                        'questions_count' => 1,
                        'points_per_question' => 2,
                        'total_points' => 2,
                        'due_date' => today()->addDays(2),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'statement' => 'Qual atitude ajuda mais um aluno a começar bem o semestre?',
                            'correct_option' => 'B',
                            'options' => [
                                'A' => 'Esperar a semana final para estudar',
                                'B' => 'Organizar rotina e revisar os canais da turma',
                                'C' => 'Ignorar os avisos da coordenação',
                                'D' => 'Focar apenas nas provas',
                            ],
                        ],
                    ],
                ],
                [
                    'activity' => [
                        'title' => 'Checklist do ambiente',
                        'description' => 'Revisao dos recursos que sustentam o trabalho na plataforma.',
                        'level' => 'media',
                        'questions_count' => 1,
                        'points_per_question' => 3,
                        'total_points' => 3,
                        'due_date' => today()->addDays(4),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'drag_drop',
                            'statement' => 'Complete: o portal organiza [blank_1] e a turma acompanha [blank_2].',
                            'keywords' => ['informacoes', 'atividades'],
                            'blank_answers' => [
                                'blank_1' => 0,
                                'blank_2' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'LOG-01' => [
                [
                    'activity' => [
                        'title' => 'Lógica aplicada em cenários reais',
                        'description' => 'Exercício de raciocínio usando exemplos do cotidiano da USCS.',
                        'level' => 'media',
                        'questions_count' => 1,
                        'points_per_question' => 2,
                        'total_points' => 2,
                        'due_date' => today()->addDays(3),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'statement' => 'O que melhor representa um algoritmo?',
                            'correct_option' => 'D',
                            'options' => [
                                'A' => 'Um tipo de monitor',
                                'B' => 'Uma pasta do sistema',
                                'C' => 'Um conjunto aleatorio de telas',
                                'D' => 'Uma sequência de passos para resolver um problema',
                            ],
                        ],
                    ],
                ],
            ],
            'DAD-01' => [
                [
                    'activity' => [
                        'title' => 'Modelo relacional básico',
                        'description' => 'Leitura de tabelas, chaves e relacionamento entre dados.',
                        'level' => 'media',
                        'questions_count' => 1,
                        'points_per_question' => 3,
                        'total_points' => 3,
                        'due_date' => today()->addDays(5),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'matching',
                            'statement' => 'Relacione o conceito ao uso correto.',
                            'left_column' => ['PK', 'FK'],
                            'right_column' => ['Identifica de forma única', 'Liga tabelas'],
                            'pairs' => [
                                '0' => '0',
                                '1' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'activity' => [
                        'title' => 'Consulta SQL guiada',
                        'description' => 'Prática inicial de consultas para a rotina acadêmica.',
                        'level' => 'dificil',
                        'questions_count' => 1,
                        'points_per_question' => 4,
                        'total_points' => 4,
                        'due_date' => today()->addDays(8),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'statement' => 'Qual comando retorna registros de uma tabela?',
                            'correct_option' => 'A',
                            'options' => [
                                'A' => 'SELECT',
                                'B' => 'CREATE',
                                'C' => 'DROP',
                                'D' => 'ALTER',
                            ],
                        ],
                    ],
                ],
            ],
            'UIX-01' => [
                [
                    'activity' => [
                        'title' => 'Fluxo de interface',
                        'description' => 'Observacao das telas e da jornada do usuario.',
                        'level' => 'facil',
                        'questions_count' => 1,
                        'points_per_question' => 2,
                        'total_points' => 2,
                        'due_date' => today()->addDays(4),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'statement' => 'Qual elemento ajuda a orientar o usuario durante a navegação?',
                            'correct_option' => 'C',
                            'options' => [
                                'A' => 'Texto aleatorio',
                                'B' => 'Espaço vazio',
                                'C' => 'Hierarquia visual clara',
                                'D' => 'Cores sem contraste',
                            ],
                        ],
                    ],
                ],
            ],
            'APP-01' => [
                [
                    'activity' => [
                        'title' => 'Estrutura do aplicativo',
                        'description' => 'Visao da organizacao interna de um app educacional.',
                        'level' => 'media',
                        'questions_count' => 1,
                        'points_per_question' => 3,
                        'total_points' => 3,
                        'due_date' => today()->addDays(6),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'drag_drop',
                            'statement' => 'Complete: a camada de [blank_1] conversa com os dados, enquanto a camada de [blank_2] exibe a interface.',
                            'keywords' => ['dados', 'apresentacao'],
                            'blank_answers' => [
                                'blank_1' => 0,
                                'blank_2' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'LAB-01' => [
                [
                    'activity' => [
                        'title' => 'Sprint integrador',
                        'description' => 'Planejamento do trabalho em equipe para consolidar as entregas.',
                        'level' => 'dificil',
                        'questions_count' => 1,
                        'points_per_question' => 4,
                        'total_points' => 4,
                        'due_date' => today()->addDays(9),
                        'status' => 'published',
                    ],
                    'questions' => [
                        [
                            'type' => 'matching',
                            'statement' => 'Associe a prática ao benefício mais adequado.',
                            'left_column' => ['Revisao', 'Dono da tarefa'],
                            'right_column' => ['Reduz erros', 'Deixa a responsabilidade clara'],
                            'pairs' => [
                                '0' => '0',
                                '1' => '1',
                            ],
                        ],
                    ],
                ],
            ],
            'ORI-01' => [
                [
                    'activity' => [
                        'title' => 'Rascunho de projeto',
                        'description' => 'Versao interna para alinhamento entre coordenação e docentes.',
                        'level' => 'facil',
                        'questions_count' => 1,
                        'points_per_question' => 1,
                        'total_points' => 1,
                        'due_date' => today()->addDays(2),
                        'status' => 'draft',
                    ],
                    'questions' => [
                        [
                            'type' => 'multiple_choice',
                            'statement' => 'Questão de rascunho para validação interna.',
                            'correct_option' => 'A',
                            'options' => [
                                'A' => 'Resposta correta',
                                'B' => 'Distrator 1',
                                'C' => 'Distrator 2',
                                'D' => 'Distrator 3',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $classrooms->each(function (Classroom $classroom) use ($activityBlueprints): void {
            collect($activityBlueprints[$classroom->code] ?? [])->each(function (array $blueprint) use ($classroom): void {
                $this->upsertActivity($classroom, $blueprint['activity'], $blueprint['questions']);
            });
        });

        $this->seedExampleSubmission($classrooms, $students);
        $this->seedAchievementProgress($classrooms, $students);
    }

    private function upsertActivity(Classroom $classroom, array $activityData, array $questions): void
    {
        $activity = Activity::query()->updateOrCreate(
            [
                'classroom_id' => $classroom->id,
                'title' => $activityData['title'],
            ],
            [
                ...$activityData,
                'teacher_id' => $classroom->teacher_id,
            ],
        );

        $activity->questions()->delete();

        collect($questions)->values()->each(function (array $question, int $index) use ($activity): void {
            $activity->questions()->create([
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
            ]);
        });
    }

    private function seedExampleSubmission($classrooms, $students): void
    {
        $classroom = $classrooms->firstWhere('code', 'FED-01');
        $student = $students->firstWhere('email', 'julia.souza@uscs.local');

        if (! $classroom || ! $student) {
            return;
        }

        $activity = Activity::query()
            ->where('classroom_id', $classroom->id)
            ->where('title', 'Boas-vindas USCS')
            ->with('questions')
            ->first();

        if (! $activity || $activity->questions->isEmpty()) {
            return;
        }

        $submission = ActivitySubmission::query()->updateOrCreate(
            [
                'activity_id' => $activity->id,
                'student_id' => $student->id,
            ],
            [
                'classroom_id' => $classroom->id,
                'status' => 'submitted',
                'submitted_at' => now()->subHours(5),
                'score' => $activity->total_points,
                'total_points' => $activity->total_points,
                'correct_answers_count' => $activity->questions->count(),
            ],
        );

        $question = $activity->questions->first();

        if (! $question) {
            return;
        }

        ActivitySubmissionAnswer::query()->updateOrCreate(
            [
                'activity_submission_id' => $submission->id,
                'activity_question_id' => $question->id,
            ],
            [
                'answer_payload' => [
                    'selected_option' => $question->correct_option,
                ],
                'is_correct' => true,
                'earned_points' => $activity->points_per_question,
            ],
        );
    }

    private function seedStudentProfiles(Collection $students): void
    {
        $profiles = [
            'julia.souza@uscs.local' => [
                'bio' => 'Gosto de começar organizado e explorar a plataforma com curiosidade.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=47',
            ],
            'lucas.pires@uscs.local' => [
                'bio' => 'Curioso por tecnologia, organização e rotinas de estudo consistentes.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=12',
            ],
            'marina.oliveira@uscs.local' => [
                'bio' => 'Aprendo melhor em atividades práticas e projetos guiados.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=32',
            ],
            'pedro.henrique@uscs.local' => [
                'bio' => 'Foco em fundamentos e trabalho em equipe para evoluir com segurança.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=57',
            ],
            'laura.martins@uscs.local' => [
                'bio' => 'Sou competitiva, mas gosto de colaborar para entregar melhor.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=20',
            ],
            'gustavo.alves@uscs.local' => [
                'bio' => 'Gosto de aprender em desafios curtos e visualmente claros.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=68',
            ],
            'beatriz.gomes@uscs.local' => [
                'bio' => 'Tenho interesse em design, usabilidade e soluções bem apresentadas.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=27',
            ],
            'renato.silva@uscs.local' => [
                'bio' => 'Gosto de analisar o problema antes de partir para a solução.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=14',
            ],
            'sophia.costa@uscs.local' => [
                'bio' => 'Prefiro atividades com etapas bem definidas e feedback rápido.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=33',
            ],
            'miguel.ferreira@uscs.local' => [
                'bio' => 'Curto explorar tecnologia de forma objetiva e aplicada.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=52',
            ],
            'isabela.nunes@uscs.local' => [
                'bio' => 'Gosto de unir comunicação clara com organização das tarefas.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=40',
            ],
            'henrique.melo@uscs.local' => [
                'bio' => 'Tenho perfil analítico e gosto de entender o sistema como um todo.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=61',
            ],
            'helena.castro@uscs.local' => [
                'bio' => 'Gosto de organizar ideias e transformar orientação em entregas claras.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=24',
            ],
            'tiago.ramos@uscs.local' => [
                'bio' => 'Prefiro desafios objetivos e acompanhamento próximo do progresso.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=66',
            ],
            'camila.azevedo@uscs.local' => [
                'bio' => 'Tenho perfil organizado e gosto de atividades com começo, meio e fim.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=23',
            ],
            'nicolas.pereira@uscs.local' => [
                'bio' => 'Curto explorar soluções técnicas e aprender com exemplos práticos.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=64',
            ],
            'aline.duarte@uscs.local' => [
                'bio' => 'Gosto de revisar detalhes e manter o ritmo de estudo constante.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=45',
            ],
            'felipe.martins@uscs.local' => [
                'bio' => 'Prefiro desafios curtos e objetivos para manter o foco.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=54',
            ],
            'raquel.lima@uscs.local' => [
                'bio' => 'Gosto de colaborar com a turma e acompanhar a evolução do grupo.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=19',
            ],
            'bruna.rocha@uscs.local' => [
                'bio' => 'Tenho interesse em organização, interface e comunicação visual.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=31',
            ],
            'caio.nogueira@uscs.local' => [
                'bio' => 'Costumo aprender bem quando consigo praticar com tarefas reais.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=59',
            ],
            'larissa.campos@uscs.local' => [
                'bio' => 'Gosto de rotina, clareza e entregas com boa apresentação.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=18',
            ],
            'otavio.mendes@uscs.local' => [
                'bio' => 'Prefiro aprender comparando exemplos e testando na prática.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=36',
            ],
        ];

        $students->each(function (User $student) use ($profiles): void {
            $profile = $profiles[$student->email] ?? [
                'bio' => 'Perfil demo do aluno USCS.',
                'github_url' => null,
                'linkedin_url' => null,
                'avatar_url' => null,
            ];

            $student->forceFill([
                'bio' => $profile['bio'],
                'github_url' => $profile['github_url'],
                'linkedin_url' => $profile['linkedin_url'],
                'photo' => $this->ensureDemoAvatar($student, $profile['avatar_url']),
            ])->save();
        });
    }

    private function ensureDemoAvatar(User $student, ?string $avatarUrl = null): string
    {
        $directory = 'users/photos/demo-pravatar';
        $slug = Str::slug(Str::before($student->email, '@'));
        $basePath = "{$directory}/{$slug}";
        $disk = Storage::disk('public');

        foreach (['png', 'jpg', 'webp', 'svg'] as $extension) {
            $existingPath = "{$basePath}.{$extension}";

            if ($disk->exists($existingPath)) {
                return $existingPath;
            }
        }

        $downloaded = $this->downloadRemoteAvatar($avatarUrl);

        if ($downloaded !== null) {
            $path = "{$basePath}.{$downloaded['extension']}";
            $disk->put($path, $downloaded['contents']);

            return $path;
        }

        $svgPath = "{$basePath}.svg";
        $disk->put($svgPath, $this->fallbackAvatarSvg($student));

        return $svgPath;
    }

    /**
     * @return array{contents: string, extension: string}|null
     */
    private function downloadRemoteAvatar(?string $avatarUrl): ?array
    {
        if (! $avatarUrl) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->retry(2, 400)
                ->get($avatarUrl);

            if (! $response->successful() || ! str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                return null;
            }

            $extension = match ($response->header('Content-Type')) {
                'image/png' => 'png',
                'image/webp' => 'webp',
                default => 'jpg',
            };

            return [
                'contents' => $response->body(),
                'extension' => $extension,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function fallbackAvatarSvg(User $student): string
    {
        $initials = Str::of($student->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $palette = [
            ['background' => '#2D1B69', 'accent' => '#8F7BFF'],
            ['background' => '#1F3A5F', 'accent' => '#4CC9F0'],
            ['background' => '#4A1D3F', 'accent' => '#FF9BD2'],
            ['background' => '#173B2F', 'accent' => '#63E6BE'],
        ];
        $colors = $palette[$student->id % count($palette)];

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256" fill="none">
  <rect width="256" height="256" rx="72" fill="{$colors['background']}"/>
  <circle cx="128" cy="128" r="90" fill="{$colors['accent']}" fill-opacity="0.18"/>
  <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="82" font-weight="700">{$initials}</text>
</svg>
SVG;
    }

    private function seedAchievementProgress(Collection $classrooms, Collection $students): void
    {
        $progressPlan = [
            [
                'classroom_code' => 'FED-01',
                'student_email' => 'julia.souza@uscs.local',
                'activity_prefix' => 'Caminho FED',
                'activity_count' => 4,
                'points_per_question' => 8.0,
                'submissions_count' => 4,
                'perfect_until' => 3,
                'streak_days' => 5,
            ],
            [
                'classroom_code' => 'LOG-01',
                'student_email' => 'marina.oliveira@uscs.local',
                'activity_prefix' => 'Rota LOG',
                'activity_count' => 3,
                'points_per_question' => 5.0,
                'submissions_count' => 3,
                'perfect_until' => 2,
                'streak_days' => 0,
            ],
            [
                'classroom_code' => 'DAD-01',
                'student_email' => 'laura.martins@uscs.local',
                'activity_prefix' => 'Labs DAD',
                'activity_count' => 4,
                'points_per_question' => 6.0,
                'submissions_count' => 4,
                'perfect_until' => 8,
                'streak_days' => 7,
            ],
            [
                'classroom_code' => 'UIX-01',
                'student_email' => 'beatriz.gomes@uscs.local',
                'activity_prefix' => 'Sprint UIX',
                'activity_count' => 3,
                'points_per_question' => 4.0,
                'submissions_count' => 3,
                'perfect_until' => 5,
                'streak_days' => 0,
            ],
            [
                'classroom_code' => 'APP-01',
                'student_email' => 'sophia.costa@uscs.local',
                'activity_prefix' => 'Mobile APP',
                'activity_count' => 3,
                'points_per_question' => 7.0,
                'submissions_count' => 3,
                'perfect_until' => 4,
                'streak_days' => 3,
            ],
            [
                'classroom_code' => 'ORI-01',
                'student_email' => 'helena.castro@uscs.local',
                'activity_prefix' => 'Projeto ORI',
                'activity_count' => 2,
                'points_per_question' => 9.0,
                'submissions_count' => 2,
                'perfect_until' => 2,
                'streak_days' => 0,
            ],
        ];

        collect($progressPlan)->each(function (array $plan) use ($classrooms, $students): void {
            $classroom = $classrooms->firstWhere('code', $plan['classroom_code']);
            $student = $students->firstWhere('email', $plan['student_email']);

            if (! $classroom || ! $student) {
                return;
            }

            $activities = $this->createSeriesActivities(
                classroom: $classroom,
                prefix: $plan['activity_prefix'],
                count: $plan['activity_count'],
                pointsPerQuestion: $plan['points_per_question'],
            );

            $this->createSeriesSubmissions(
                activities: $activities,
                student: $student,
                submissionsCount: $plan['submissions_count'],
                perfectUntil: $plan['perfect_until'],
                streakDays: $plan['streak_days'],
            );
        });
    }

    private function createSeriesActivities(
        Classroom $classroom,
        string $prefix,
        int $count,
        float $pointsPerQuestion,
    ): Collection {
        return collect(range(1, $count))
            ->map(function (int $index) use ($classroom, $prefix, $pointsPerQuestion) {
                $title = "{$prefix} {$index}";

                $this->upsertActivity($classroom, [
                    'title' => $title,
                    'description' => "Atividade {$index} da trilha {$prefix}.",
                    'level' => $index % 3 === 0 ? 'dificil' : ($index % 2 === 0 ? 'media' : 'facil'),
                    'questions_count' => 1,
                    'points_per_question' => $pointsPerQuestion,
                    'total_points' => $pointsPerQuestion,
                    'due_date' => today()->addDays(max(1, $index)),
                    'status' => 'published',
                ], [
                    [
                        'type' => 'multiple_choice',
                        'statement' => "Questão {$index} da trilha {$prefix}.",
                        'correct_option' => 'A',
                        'options' => [
                            'A' => 'Resposta correta',
                            'B' => 'Distrator 1',
                            'C' => 'Distrator 2',
                            'D' => 'Distrator 3',
                        ],
                    ],
                ]);

                return Activity::query()
                    ->where('classroom_id', $classroom->id)
                    ->where('title', $title)
                    ->with('questions')
                    ->firstOrFail();
            })
            ->values();
    }

    private function createSeriesSubmissions(
        Collection $activities,
        User $student,
        int $submissionsCount,
        int $perfectUntil,
        int $streakDays,
    ): void {
        $activities->take($submissionsCount)->values()->each(function (Activity $activity, int $index) use ($student, $perfectUntil, $streakDays): void {
            $isPerfect = $index < $perfectUntil;
            $submittedAt = $this->submissionTimestampForIndex($index, $streakDays);
            $question = $activity->questions->first();

            $submission = ActivitySubmission::query()->updateOrCreate(
                [
                    'activity_id' => $activity->id,
                    'student_id' => $student->id,
                ],
                [
                    'classroom_id' => $activity->classroom_id,
                    'status' => 'submitted',
                    'submitted_at' => $submittedAt,
                    'score' => $isPerfect ? $activity->total_points : 0,
                    'total_points' => $activity->total_points,
                    'correct_answers_count' => $isPerfect ? $activity->questions->count() : 0,
                ],
            );

            if (! $question) {
                return;
            }

            ActivitySubmissionAnswer::query()->updateOrCreate(
                [
                    'activity_submission_id' => $submission->id,
                    'activity_question_id' => $question->id,
                ],
                [
                    'answer_payload' => [
                        'selected_option' => $isPerfect ? $question->correct_option : 'B',
                    ],
                    'is_correct' => $isPerfect,
                    'earned_points' => $isPerfect ? $activity->points_per_question : 0,
                ],
            );
        });
    }

    private function submissionTimestampForIndex(int $index, int $streakDays): \Carbon\CarbonInterface
    {
        if ($index < $streakDays) {
            return now()->startOfDay()->subDays(($streakDays - 1) - $index)->addHours(9);
        }

        return now()->subDays($index + 10)->addHours(14);
    }
}
