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
use App\Models\User;
use App\Support\PerformanceMetricsRebuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
                'name' => 'Escola Central TankCode',
                'cnpj' => $schoolCnpj,
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

            DB::table('classroom_student')
                ->whereIn('user_id', $studentIds)
                ->where('classroom_id', '!=', $classroom->id)
                ->delete();

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

        $this->seedStudentProfiles($students);
        $classrooms->each(fn (Classroom $classroom) => $classroom->touch());
        $this->seedActivities($classrooms, $students);

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
        $classrooms->each(function (Classroom $classroom): void {
            if ($classroom->code === 'GAMA-01') {
                $this->upsertActivity($classroom, [
                    'title' => 'Desafio de logica',
                    'description' => 'Resolva os exercicios introdutorios enviados para a Turma Gama.',
                    'level' => 'facil',
                    'questions_count' => 1,
                    'points_per_question' => 1,
                    'total_points' => 1,
                    'due_date' => today(),
                    'status' => 'published',
                ], [
                    [
                        'type' => 'multiple_choice',
                        'statement' => 'Qual estrutura guarda uma sequência de instruções?',
                        'correct_option' => 'B',
                        'options' => [
                            'A' => 'Mouse',
                            'B' => 'Algoritmo',
                            'C' => 'Monitor',
                            'D' => 'Teclado',
                        ],
                    ],
                ]);

                $this->upsertActivity($classroom, [
                    'title' => 'Quiz de programação',
                    'description' => 'Responda ao quiz e revise os conceitos vistos nesta semana.',
                    'level' => 'media',
                    'questions_count' => 1,
                    'points_per_question' => 2.5,
                    'total_points' => 2.5,
                    'due_date' => today()->addDays(4),
                    'status' => 'published',
                ], [
                    [
                        'type' => 'drag_drop',
                        'statement' => 'Complete: HTML define a [blank_1] e CSS define o [blank_2].',
                        'keywords' => ['estrutura', 'estilo'],
                        'blank_answers' => [
                            'blank_1' => 0,
                            'blank_2' => 1,
                        ],
                    ],
                ]);

                $this->upsertActivity($classroom, [
                    'title' => 'Mini projeto em dupla',
                    'description' => 'Planeje a entrega do prototipo com sua dupla e registre a evolução.',
                    'level' => 'dificil',
                    'questions_count' => 1,
                    'points_per_question' => 4,
                    'total_points' => 4,
                    'due_date' => today()->addDays(9),
                    'status' => 'published',
                ], [
                    [
                        'type' => 'matching',
                        'statement' => 'Relacione os conceitos aos seus significados.',
                        'left_column' => ['Variavel', 'Loop'],
                        'right_column' => ['Repetição', 'Armazena um valor'],
                        'pairs' => [
                            '0' => '1',
                            '1' => '0',
                        ],
                    ],
                ]);

                $this->upsertActivity($classroom, [
                    'title' => 'Rascunho interno da turma',
                    'description' => 'Esta atividade deve permanecer oculta para os alunos.',
                    'level' => 'facil',
                    'questions_count' => 1,
                    'points_per_question' => 1,
                    'total_points' => 1,
                    'due_date' => today()->addDays(2),
                    'status' => 'draft',
                ], [
                    [
                        'type' => 'multiple_choice',
                        'statement' => 'Questao em rascunho.',
                        'correct_option' => 'A',
                        'options' => [
                            'A' => 'Resposta correta',
                            'B' => 'Distrator 1',
                            'C' => 'Distrator 2',
                            'D' => 'Distrator 3',
                        ],
                    ],
                ]);
            }
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
        $classroom = $classrooms->firstWhere('code', 'GAMA-01');
        $student = $students->firstWhere('email', 'laura.alves@escola.local');

        if (! $classroom || ! $student) {
            return;
        }

        $activity = Activity::query()
            ->where('classroom_id', $classroom->id)
            ->where('title', 'Desafio de logica')
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
            'julia.martins@escola.local' => [
                'bio' => 'Adoro resolver desafios de lógica e criar interfaces coloridas.',
                'github_url' => 'https://github.com/juliamartins-demo',
                'linkedin_url' => 'https://www.linkedin.com/in/juliamartins-demo/',
                'avatar_url' => 'https://i.pravatar.cc/300?img=47',
            ],
            'lucas.ferreira@escola.local' => [
                'bio' => 'Sou curioso por desenvolvimento web e gosto de criar automações simples.',
                'github_url' => 'https://github.com/lucasferreira-demo',
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=12',
            ],
            'marina.costa@escola.local' => [
                'bio' => 'Gosto de aprender praticando e manter uma rotina consistente de estudos.',
                'github_url' => 'https://github.com/marinacosta-demo',
                'linkedin_url' => 'https://www.linkedin.com/in/marinacosta-demo/',
                'avatar_url' => 'https://i.pravatar.cc/300?img=32',
            ],
            'pedro.henrique@escola.local' => [
                'bio' => 'Estou focado em fortalecer meus fundamentos de programação e evoluir em projetos em equipe.',
                'github_url' => null,
                'linkedin_url' => 'https://www.linkedin.com/in/pedrohenrique-demo/',
                'avatar_url' => 'https://i.pravatar.cc/300?img=57',
            ],
            'laura.alves@escola.local' => [
                'bio' => 'Gosto de competir comigo mesma para subir no ranking e bater novas metas.',
                'github_url' => 'https://github.com/lauraalves-demo',
                'linkedin_url' => 'https://www.linkedin.com/in/lauraalves-demo/',
                'avatar_url' => 'https://i.pravatar.cc/300?img=20',
            ],
            'gustavo.rocha@escola.local' => [
                'bio' => 'Exploro programação por meio de quizzes e mini projetos para aprender na prática.',
                'github_url' => 'https://github.com/gustavorocha-demo',
                'linkedin_url' => null,
                'avatar_url' => 'https://i.pravatar.cc/300?img=68',
            ],
        ];

        $students->each(function (User $student) use ($profiles): void {
            $profile = $profiles[$student->email] ?? [
                'bio' => 'Perfil demo do aluno TankCode.',
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
                'classroom_code' => 'ALFA-01',
                'student_email' => 'julia.martins@escola.local',
                'activity_prefix' => 'Trilha Alfa',
                'activity_count' => 10,
                'points_per_question' => 10.0,
                'submissions_count' => 10,
                'perfect_until' => 10,
                'streak_days' => 0,
            ],
            [
                'classroom_code' => 'ALFA-01',
                'student_email' => 'lucas.ferreira@escola.local',
                'activity_prefix' => 'Sprint Alfa',
                'activity_count' => 4,
                'points_per_question' => 5.0,
                'submissions_count' => 2,
                'perfect_until' => 1,
                'streak_days' => 0,
            ],
            [
                'classroom_code' => 'BETA-01',
                'student_email' => 'marina.costa@escola.local',
                'activity_prefix' => 'Jornada Beta',
                'activity_count' => 25,
                'points_per_question' => 8.0,
                'submissions_count' => 25,
                'perfect_until' => 12,
                'streak_days' => 7,
            ],
            [
                'classroom_code' => 'BETA-01',
                'student_email' => 'pedro.henrique@escola.local',
                'activity_prefix' => 'Laboratorio Beta',
                'activity_count' => 12,
                'points_per_question' => 6.0,
                'submissions_count' => 10,
                'perfect_until' => 5,
                'streak_days' => 0,
            ],
            [
                'classroom_code' => 'GAMA-01',
                'student_email' => 'laura.alves@escola.local',
                'activity_prefix' => 'Maratona Gama',
                'activity_count' => 50,
                'points_per_question' => 20.0,
                'submissions_count' => 50,
                'perfect_until' => 50,
                'streak_days' => 0,
            ],
            [
                'classroom_code' => 'GAMA-01',
                'student_email' => 'gustavo.rocha@escola.local',
                'activity_prefix' => 'Desafios Gama',
                'activity_count' => 8,
                'points_per_question' => 4.0,
                'submissions_count' => 3,
                'perfect_until' => 1,
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
