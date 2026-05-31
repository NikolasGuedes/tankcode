<?php

namespace App\Http\Controllers\Student;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Classroom;
use App\Models\StudentAchievement;
use App\Models\StudentClassroomPerformance;
use App\Models\User;
use App\Support\StudentAchievementSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function classroom(Request $request): Response
    {
        $student = $this->resolveStudent($request);
        $classroom = $student->classrooms->first();
        $point = $student->pointOfSchools->first();
        $score = $this->scoreSummary($student, $classroom);
        $activityFilters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'in:all,respondida,vence_hoje,vence_semana,atrasada,pendente'],
            'sort' => ['nullable', 'string', 'in:deadline_asc,deadline_desc,title_asc,title_desc,newest'],
        ]);
        $classmates = $this->classroomRanking($classroom, $student);

        return Inertia::render('student/Classroom', [
            'classroom' => [
                'name' => $classroom?->name ?? 'Sala em configuração',
                'code' => $classroom?->code ?? 'Sem código',
                'point_of_school' => $classroom?->pointOfSchool?->name ?? $point?->name ?? 'Ponto de ensino não definido',
                'teacher' => $classroom?->teacher?->name ?? 'Professor em definição',
            ],
            'score' => [
                'student_points' => $score['student_points'],
                'classroom_rank' => $score['classroom_rank'],
            ],
            'activities' => $this->classroomActivities(
                student: $student,
                classroomId: $classroom?->id,
                filters: $activityFilters,
            ),
            'activity_filters' => [
                'search' => $activityFilters['search'] ?? '',
                'state' => $activityFilters['state'] ?? 'all',
                'sort' => $activityFilters['sort'] ?? 'deadline_asc',
            ],
            'activity_summary' => $this->classroomActivitySummary($student, $classroom?->id),
            'classmates' => $classmates,
        ]);
    }

    public function profile(Request $request): Response
    {
        $student = $this->resolveStudent($request);
        $classroom = $student->classrooms->first();

        return Inertia::render('student/Profile', [
            'profile' => $this->profileData($student, $classroom),
            'viewer_mode' => false,
            'update_url' => route('student.profile.update', absolute: false),
        ]);
    }

    public function updateProfile(Request $request, StudentAchievementSyncService $achievementSyncService): RedirectResponse
    {
        $student = $this->resolveStudent($request);
        $section = $request->validate([
            'section' => ['required', 'string', 'in:bio,links,photo'],
        ])['section'];

        if ($section === 'bio') {
            $data = $request->validate([
                'bio' => ['nullable', 'string', 'max:1000'],
            ]);

            $student->update([
                'bio' => $this->nullableString($data['bio'] ?? null),
            ]);
            $newlyUnlockedAchievements = $achievementSyncService->syncStudent($student->fresh());

            return $this->withAchievementUnlocks(
                back()->with('success', 'Bio atualizada com sucesso.'),
                $newlyUnlockedAchievements,
            );
        }

        if ($section === 'links') {
            $data = $request->validate([
                'github_url' => ['nullable', 'url', 'max:255'],
                'linkedin_url' => ['nullable', 'url', 'max:255'],
            ]);

            $student->update([
                'github_url' => $this->nullableString($data['github_url'] ?? null),
                'linkedin_url' => $this->nullableString($data['linkedin_url'] ?? null),
            ]);
            $newlyUnlockedAchievements = $achievementSyncService->syncStudent($student->fresh());

            return $this->withAchievementUnlocks(
                back()->with('success', 'Links atualizados com sucesso.'),
                $newlyUnlockedAchievements,
            );
        }

        $data = $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->update([
            'photo' => $data['photo']->store('users/photos', 'public'),
        ]);
        $newlyUnlockedAchievements = $achievementSyncService->syncStudent($student->fresh());

        return $this->withAchievementUnlocks(
            back()->with('success', 'Foto atualizada com sucesso.'),
            $newlyUnlockedAchievements,
        );
    }

    public function show(Request $request, User $student): Response
    {
        $viewer = $this->resolveStudent($request);
        $viewerClassroom = $viewer->classrooms->first();
        $viewerPointIds = $viewer->pointOfSchools->pluck('id');
        $viewerSchoolId = $viewer->school_id;

        abort_unless($viewerClassroom || $viewerPointIds->isNotEmpty() || $viewerSchoolId, 404);

        $student->loadMissing([
            'role:id,name,label',
            'school:id,name',
            'pointOfSchools:id,name',
            'classrooms:id,name,code,point_of_school_id,teacher_id',
            'classrooms.pointOfSchool:id,name',
            'classrooms.teacher:id,name',
        ]);

        abort_unless($student->hasRole(RoleEnum::STUDENT), 404);
        $sharesClassroom = $viewerClassroom && $student->classrooms->contains(fn ($classroom) => $classroom->id === $viewerClassroom->id);
        $sharesPoint = $student->pointOfSchools->pluck('id')->intersect($viewerPointIds)->isNotEmpty();
        $sharesSchool = $viewerSchoolId && $student->school_id === $viewerSchoolId;

        abort_unless($sharesClassroom || $sharesPoint || $sharesSchool, 404);

        $studentClassroom = $viewerClassroom
            ? ($student->classrooms->firstWhere('id', $viewerClassroom->id) ?? $student->classrooms->first())
            : $student->classrooms->first();

        return Inertia::render('student/Show', [
            'profile' => $this->profileData($student, $studentClassroom),
            'viewer_mode' => true,
            'viewer_label' => $sharesClassroom ? 'Visualização do colega' : 'Visualização do ranking global',
        ]);
    }

    public function scoreGlobal(Request $request): Response
    {
        $student = $this->resolveStudent($request);
        $classroom = $student->classrooms->first();
        $point = $student->pointOfSchools->first();
        $score = $this->scoreSummary($student, $classroom);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'classroom' => ['nullable', 'string', 'max:20'],
            'unit' => ['nullable', 'string', 'max:20'],
        ]);
        $ranking = $this->schoolRanking(
            viewer: $student,
            search: $filters['search'] ?? null,
            classroomId: $this->normalizeFilterId($filters['classroom'] ?? null),
            unitId: $this->normalizeFilterId($filters['unit'] ?? null),
        );

        return Inertia::render('student/ScoreGlobal', [
            'score' => [
                'student_points' => $score['student_points'],
                'classroom_rank' => $score['classroom_rank'],
                'point_rank' => $score['point_rank'],
                'school_rank' => $score['school_rank'],
            ],
            'classroom' => [
                'name' => $classroom?->name ?? 'Sala em configuração',
            ],
            'point' => [
                'name' => $point?->name ?? 'Ponto de ensino em configuração',
            ],
            'school' => [
                'name' => $student->school?->name ?? 'Escola em configuração',
            ],
            'students' => $ranking,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'classroom' => $filters['classroom'] ?? 'all',
                'unit' => $filters['unit'] ?? 'all',
            ],
            'filter_options' => [
                'classrooms' => $this->schoolClassroomOptions($student),
                'units' => $this->schoolUnitOptions($student),
            ],
        ]);
    }

    private function resolveStudent(Request $request): User
    {
        /** @var User $student */
        $student = $request->user()->loadMissing([
            'role:id,name,label',
            'school:id,name',
            'pointOfSchools:id,name',
            'classrooms:id,name,code,point_of_school_id,teacher_id',
            'classrooms.pointOfSchool:id,name',
            'classrooms.teacher:id,name',
            'classrooms.students:id,name,email,photo,school_id,role_id,status',
            'classrooms.students.role:id,name,label',
            'studentAchievements:id,student_id,achievement_id,awarded_at',
            'studentAchievements.achievement:id,code,name,description,image_path,sort_order,is_active',
        ]);

        return $student;
    }

    /**
     * @return array{
     *     name: string,
     *     email: string,
     *     avatar: string|null,
     *     points: float,
     *     bio: string,
     *     links: array{github: string|null, linkedin: string|null},
     *     status: array{completed_activities: int, ranking_position: int},
     *     achievements: array<int, array{code: string, title: string, description: string, image_url: string, is_unlocked: bool, unlocked_at: string|null}>,
     *     achievement_summary: array{earned_count: int, total_count: int},
     *     classroom: array{name: string, code: string, point_of_school: string, teacher: string}
     * }
     */
    private function profileData(User $student, mixed $classroom): array
    {
        $point = $student->pointOfSchools->first();
        $score = $this->scoreSummary($student, $classroom);
        $achievements = $this->achievementPayload($student);

        return [
            'name' => $student->name,
            'email' => $student->email,
            'avatar' => $student->photo ? asset('storage/'.$student->photo) : null,
            'points' => $score['student_points'],
            'bio' => $student->bio ?: 'Espaço reservado para a bio do aluno. Quando esse campo estiver disponível, ele aparecerá aqui.',
            'bio_raw' => $student->bio,
            'links' => [
                'github' => $student->github_url,
                'linkedin' => $student->linkedin_url,
            ],
            'status' => [
                'completed_activities' => $score['submitted_activities_count'],
                'ranking_position' => $score['school_rank'],
            ],
            'achievements' => $achievements,
            'achievement_summary' => [
                'earned_count' => collect($achievements)->where('is_unlocked', true)->count(),
                'total_count' => count($achievements),
            ],
            'classroom' => [
                'name' => $classroom?->name ?? 'Sala em configuração',
                'code' => $classroom?->code ?? 'Sem código',
                'point_of_school' => $classroom?->pointOfSchool?->name ?? $point?->name ?? 'Ponto de ensino não definido',
                'teacher' => $classroom?->teacher?->name ?? 'Professor em definição',
            ],
        ];
    }

    /**
     * @return array{
     *     student_points: float,
     *     classroom_rank: int,
     *     point_rank: int,
     *     school_rank: int,
     *     submitted_activities_count: int
     * }
     */
    private function scoreSummary(User $student, mixed $classroom): array
    {
        $performance = $this->performanceForStudent($student, $classroom?->id);

        return [
            'student_points' => round((float) ($performance?->total_score ?? 0), 2),
            'classroom_rank' => (int) ($performance?->classroom_rank ?? 0),
            'point_rank' => (int) ($performance?->point_rank ?? 0),
            'school_rank' => (int) ($performance?->school_rank ?? 0),
            'submitted_activities_count' => (int) ($performance?->submitted_activities_count ?? 0),
        ];
    }

    /**
     * @param  array{search?: string|null, state?: string|null, sort?: string|null}  $filters
     * @return array{
     *     data: array<int, array{id: int, title: string, description: string, due_date: string|null, deadline_label: string, deadline_group: string, state: string, href: string, submitted_at: string|null, score: mixed, total_points: mixed}>,
     *     links: array<int, array{url: string|null, label: string, active: bool}>,
     *     current_page: int,
     *     last_page: int,
     *     from: int|null,
     *     to: int|null,
     *     total: int
     * }
     */
    private function classroomActivities(User $student, ?int $classroomId, array $filters): array
    {
        if (! $classroomId) {
            return [
                'data' => [],
                'links' => [],
                'current_page' => 1,
                'last_page' => 1,
                'from' => null,
                'to' => null,
                'total' => 0,
            ];
        }

        $query = Activity::query()
            ->with([
                'submissions' => fn ($query) => $query->where('student_id', $student->id),
            ])
            ->where('classroom_id', $classroomId)
            ->where('status', 'published');

        if (($filters['search'] ?? null) !== null && trim((string) $filters['search']) !== '') {
            $term = trim((string) $filters['search']);
            $query->where(function ($nested) use ($term): void {
                $nested
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $this->applyActivityStateFilter($query, $student, $filters['state'] ?? 'all');
        $this->applyActivitySort($query, $filters['sort'] ?? 'deadline_asc');

        return $query
            ->paginate(6)
            ->withQueryString()
            ->through(function (Activity $activity) {
                $submission = $activity->submissions->first();
                $state = $this->activityState($activity, $submission !== null);

                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description ?? 'Atividade publicada para sua turma.',
                    'due_date' => optional($activity->due_date)?->format('Y-m-d'),
                    'deadline_label' => $this->activityDeadlineLabel($activity, $submission !== null),
                    'deadline_group' => $this->activityDeadlineGroup($activity),
                    'state' => $state,
                    'href' => route('student.activities.show', $activity, absolute: false),
                    'submitted_at' => optional($submission?->submitted_at)?->toIso8601String(),
                    'score' => $submission?->score,
                    'total_points' => $activity->total_points,
                ];
            })
            ->toArray();
    }

    /**
     * @return array<int, array{label: string, count: int}>
     */
    private function classroomActivitySummary(User $student, ?int $classroomId): array
    {
        if (! $classroomId) {
            return [];
        }

        $activities = Activity::query()
            ->with([
                'submissions' => fn ($query) => $query->where('student_id', $student->id),
            ])
            ->where('classroom_id', $classroomId)
            ->where('status', 'published')
            ->get();

        return [
            [
                'label' => 'Hoje',
                'count' => $activities->filter(fn (Activity $activity) => $this->activityDeadlineGroup($activity) === 'Hoje')->count(),
            ],
            [
                'label' => 'Essa semana',
                'count' => $activities->filter(fn (Activity $activity) => $this->activityDeadlineGroup($activity) === 'Essa semana')->count(),
            ],
            [
                'label' => 'Proximas',
                'count' => $activities->filter(fn (Activity $activity) => $this->activityDeadlineGroup($activity) === 'Proximas')->count(),
            ],
            [
                'label' => 'Respondidas',
                'count' => $activities->filter(fn (Activity $activity) => $activity->submissions->isNotEmpty())->count(),
            ],
        ];
    }

    private function classroomRanking(?Classroom $classroom, User $viewer): array
    {
        if (! $classroom) {
            return [];
        }

        return StudentClassroomPerformance::query()
            ->with('student:id,name,email,photo')
            ->where('classroom_id', $classroom->id)
            ->orderBy('classroom_rank')
            ->get()
            ->map(fn (StudentClassroomPerformance $performance) => $this->rankingEntry(
                performance: $performance,
                viewer: $viewer,
                rankingPosition: (int) $performance->classroom_rank,
            ))
            ->values()
            ->all();
    }

    private function schoolRanking(User $viewer, ?string $search = null, ?int $classroomId = null, ?int $unitId = null): array
    {
        if (! $viewer->school_id) {
            return [];
        }

        return StudentClassroomPerformance::query()
            ->with([
                'student:id,name,email,photo',
                'classroom:id,name',
                'pointOfSchool:id,name',
            ])
            ->where('school_id', $viewer->school_id)
            ->orderBy('school_rank')
            ->when($search, function ($query, string $term) {
                $query->whereHas('student', fn ($nested) => $nested
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($classroomId, fn ($query, int $id) => $query->where('classroom_id', $id))
            ->when($unitId, fn ($query, int $id) => $query->where('point_of_school_id', $id))
            ->get()
            ->map(fn (StudentClassroomPerformance $performance) => $this->rankingEntry(
                performance: $performance,
                viewer: $viewer,
                rankingPosition: (int) $performance->school_rank,
            ))
            ->values()
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     avatar: string|null,
     *     score: float,
     *     ranking_position: int,
     *     href: string,
     *     is_current_user: bool,
     *     classroom_name: string,
     *     unit_name: string
     * }
     */
    private function rankingEntry(StudentClassroomPerformance $performance, User $viewer, int $rankingPosition): array
    {
        $student = $performance->student;

        return [
            'id' => $student?->id ?? 0,
            'name' => $student?->name ?? 'Aluno não encontrado',
            'email' => $student?->email ?? '-',
            'avatar' => $student?->photo ? asset('storage/'.$student->photo) : null,
            'score' => round((float) $performance->total_score, 2),
            'ranking_position' => $rankingPosition,
            'href' => $student ? route('student.classmates.show', $student, absolute: false) : '#',
            'is_current_user' => $student?->is($viewer) ?? false,
            'classroom_name' => $performance->classroom?->name ?? 'Sala em configuração',
            'unit_name' => $performance->pointOfSchool?->name ?? 'Unidade em configuração',
        ];
    }

    private function schoolClassroomOptions(User $viewer): array
    {
        if (! $viewer->school_id) {
            return [];
        }

        return Classroom::query()
            ->where('school_id', $viewer->school_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Classroom $classroom) => [
                'id' => $classroom->id,
                'name' => $classroom->name,
            ])
            ->values()
            ->all();
    }

    private function schoolUnitOptions(User $viewer): array
    {
        if (! $viewer->school_id) {
            return [];
        }

        return $viewer->school
            ? $viewer->school->pointOfSchools()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($unit) => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                ])
                ->values()
                ->all()
            : [];
    }

    private function performanceForStudent(User $student, ?int $classroomId = null): ?StudentClassroomPerformance
    {
        $query = StudentClassroomPerformance::query()->where('student_id', $student->id);

        if ($classroomId) {
            $performance = (clone $query)->where('classroom_id', $classroomId)->first();

            if ($performance) {
                return $performance;
            }
        }

        return $query
            ->orderByDesc('total_score')
            ->orderBy('school_rank')
            ->first();
    }

    private function normalizeFilterId(?string $value): ?int
    {
        if (! $value || $value === 'all' || ! ctype_digit($value)) {
            return null;
        }

        return (int) $value;
    }

    private function nullableString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    /**
     * @return array<int, array{code: string, title: string, description: string, image_url: string, is_unlocked: bool, unlocked_at: string|null}>
     */
    private function achievementPayload(User $student): array
    {
        $catalog = Achievement::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'code', 'name', 'description', 'image_path']);

        $awarded = $student->studentAchievements
            ->keyBy('achievement_id');

        return $catalog
            ->map(function (Achievement $achievement) use ($awarded) {
                $studentAchievement = $awarded->get($achievement->id);

                return [
                    'code' => $achievement->code,
                    'title' => $achievement->name,
                    'description' => $achievement->description,
                    'image_url' => asset($achievement->image_path),
                    'is_unlocked' => $studentAchievement !== null,
                    'unlocked_at' => optional($studentAchievement?->awarded_at)?->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    private function activityState(Activity $activity, bool $submitted): string
    {
        if ($submitted) {
            return 'respondida';
        }

        if (! $activity->due_date) {
            return 'pendente';
        }

        if ($activity->due_date->isPast() && ! $activity->due_date->isToday()) {
            return 'atrasada';
        }

        if ($activity->due_date->isToday()) {
            return 'vence_hoje';
        }

        if ($activity->due_date->isBetween(today(), today()->copy()->addDays(6))) {
            return 'vence_semana';
        }

        return 'pendente';
    }

    private function applyActivitySort(mixed $query, string $sort): void
    {
        match ($sort) {
            'deadline_desc' => $query
                ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                ->orderByDesc('due_date')
                ->latest('id'),
            'title_asc' => $query
                ->orderBy('title')
                ->latest('id'),
            'title_desc' => $query
                ->orderByDesc('title')
                ->latest('id'),
            'newest' => $query->latest('id'),
            default => $query
                ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('due_date')
                ->latest('id'),
        };
    }

    private function applyActivityStateFilter(mixed $query, User $student, string $state): void
    {
        $today = today()->toDateString();
        $weekEnd = today()->copy()->addDays(6)->toDateString();

        match ($state) {
            'respondida' => $query->whereHas('submissions', fn ($nested) => $nested->where('student_id', $student->id)),
            'vence_hoje' => $query
                ->whereDoesntHave('submissions', fn ($nested) => $nested->where('student_id', $student->id))
                ->whereDate('due_date', $today),
            'vence_semana' => $query
                ->whereDoesntHave('submissions', fn ($nested) => $nested->where('student_id', $student->id))
                ->whereBetween('due_date', [
                    today()->copy()->addDay()->toDateString(),
                    $weekEnd,
                ]),
            'atrasada' => $query
                ->whereDoesntHave('submissions', fn ($nested) => $nested->where('student_id', $student->id))
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today),
            'pendente' => $query
                ->whereDoesntHave('submissions', fn ($nested) => $nested->where('student_id', $student->id))
                ->where(function ($nested) use ($weekEnd, $today): void {
                    $nested
                        ->whereNull('due_date')
                        ->orWhereDate('due_date', '>', $weekEnd);
                }),
            default => null,
        };
    }

    private function activityDeadlineLabel(Activity $activity, bool $submitted): string
    {
        if ($submitted) {
            return 'Respondida';
        }

        if (! $activity->due_date) {
            return 'Sem prazo';
        }

        if ($activity->due_date->isPast() && ! $activity->due_date->isToday()) {
            return 'Prazo encerrado';
        }

        if ($activity->due_date->isToday()) {
            return 'Entrega hoje';
        }

        if ($activity->due_date->isBetween(today(), today()->copy()->addDays(6))) {
            return 'Entrega nesta semana';
        }

        return 'Entrega futura';
    }

    private function activityDeadlineGroup(Activity $activity): string
    {
        if (! $activity->due_date) {
            return 'Proximas';
        }

        if ($activity->due_date->isToday()) {
            return 'Hoje';
        }

        if ($activity->due_date->isBetween(today(), today()->copy()->addDays(6))) {
            return 'Essa semana';
        }

        return 'Proximas';
    }

    /**
     * @param  Collection<int, StudentAchievement>  $studentAchievements
     */
    private function withAchievementUnlocks(RedirectResponse $response, Collection $studentAchievements): RedirectResponse
    {
        if ($studentAchievements->isEmpty()) {
            return $response;
        }

        return $response->with('achievement_unlocks', $studentAchievements
            ->sortBy(fn (StudentAchievement $studentAchievement) => [
                $studentAchievement->awarded_at?->getTimestamp() ?? 0,
                $studentAchievement->achievement?->sort_order ?? 0,
            ])
            ->values()
            ->map(fn (StudentAchievement $studentAchievement) => [
                'code' => $studentAchievement->achievement?->code ?? '',
                'title' => $studentAchievement->achievement?->name ?? 'Conquista',
                'description' => $studentAchievement->achievement?->description ?? 'Nova conquista desbloqueada.',
                'image_url' => $studentAchievement->achievement?->image_path
                    ? asset($studentAchievement->achievement->image_path)
                    : null,
                'awarded_at' => optional($studentAchievement->awarded_at)?->toIso8601String(),
            ])
            ->all());
    }
}
