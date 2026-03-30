<?php

namespace App\Http\Controllers\Student;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\PointOfSchool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
        $score = $this->mockedScore($student, $classroom);
        $classmates = $this->classroomRanking($classroom, $student);

        return Inertia::render('student/Classroom', [
            'classroom' => [
                'name' => $classroom?->name ?? 'Sala em configuracao',
                'code' => $classroom?->code ?? 'Sem codigo',
                'point_of_school' => $classroom?->pointOfSchool?->name ?? $point?->name ?? 'Ponto de ensino nao definido',
                'teacher' => $classroom?->teacher?->name ?? 'Professor em definicao',
            ],
            'score' => [
                'student_points' => $score['student_points'],
                'classroom_rank' => $score['classroom_rank'],
            ],
            'activities' => $this->mockedActivities($classroom?->name ?? 'Minha sala'),
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

    public function updateProfile(Request $request): RedirectResponse
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

            return back()->with('success', 'Bio atualizada com sucesso.');
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

            return back()->with('success', 'Links atualizados com sucesso.');
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

        return back()->with('success', 'Foto atualizada com sucesso.');
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
            'viewer_label' => $sharesClassroom ? 'Visualizacao do colega' : 'Visualizacao do ranking global',
        ]);
    }

    public function scoreGlobal(Request $request): Response
    {
        $student = $this->resolveStudent($request);
        $classroom = $student->classrooms->first();
        $point = $student->pointOfSchools->first();
        $score = $this->mockedScore($student, $classroom);
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
            ],
            'classroom' => [
                'name' => $classroom?->name ?? 'Sala em configuracao',
            ],
            'point' => [
                'name' => $point?->name ?? 'Ponto de ensino em configuracao',
            ],
            'school' => [
                'name' => $student->school?->name ?? 'Escola em configuracao',
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
        ]);

        return $student;
    }

    /**
     * @return array{
     *     name: string,
     *     email: string,
     *     avatar: string|null,
     *     points: int,
     *     bio: string,
     *     links: array{github: string|null, linkedin: string|null},
     *     status: array{completed_activities: int, ranking_position: int},
     *     classroom: array{name: string, code: string, point_of_school: string, teacher: string}
     * }
     */
    private function profileData(User $student, mixed $classroom): array
    {
        $point = $student->pointOfSchools->first();
        $score = $this->mockedScore($student, $classroom);

        return [
            'name' => $student->name,
            'email' => $student->email,
            'avatar' => $student->photo ? asset('storage/'.$student->photo) : null,
            'points' => $score['student_points'],
            'bio' => $student->bio ?: 'Espaco reservado para a bio do aluno. Quando esse campo estiver disponivel, ele aparecera aqui.',
            'bio_raw' => $student->bio,
            'links' => [
                'github' => $student->github_url,
                'linkedin' => $student->linkedin_url,
            ],
            'status' => [
                'completed_activities' => 5,
                'ranking_position' => 155,
            ],
            'classroom' => [
                'name' => $classroom?->name ?? 'Sala em configuracao',
                'code' => $classroom?->code ?? 'Sem codigo',
                'point_of_school' => $classroom?->pointOfSchool?->name ?? $point?->name ?? 'Ponto de ensino nao definido',
                'teacher' => $classroom?->teacher?->name ?? 'Professor em definicao',
            ],
        ];
    }

    /**
     * @return array{
     *     student_points: int,
     *     classroom_rank: int,
     *     point_rank: int
     * }
     */
    private function mockedScore(User $student, mixed $classroom): array
    {
        $seed = ($student->id * 37) + (($classroom?->id ?? 1) * 19);

        return [
            'student_points' => 350 + ($seed % 220),
            'classroom_rank' => 1 + ($seed % 8),
            'point_rank' => 1 + ($seed % 18),
        ];
    }

    /**
     * @return array<int, array{
     *     id: string,
     *     title: string,
     *     description: string,
     *     deadline_label: string,
     *     deadline_group: string,
     *     status: string
     * }>
     */
    private function mockedActivities(string $classroomName): array
    {
        return [
            [
                'id' => 'activity-1',
                'title' => 'Desafio de logica',
                'description' => "Resolva os exercicios introdutorios enviados para {$classroomName}.",
                'deadline_label' => 'Entrega hoje',
                'deadline_group' => 'Hoje',
                'status' => 'urgent',
            ],
            [
                'id' => 'activity-2',
                'title' => 'Quiz de programacao',
                'description' => 'Responda ao quiz e revise os conceitos vistos nesta semana.',
                'deadline_label' => 'Entrega ate sexta',
                'deadline_group' => 'Essa semana',
                'status' => 'warning',
            ],
            [
                'id' => 'activity-3',
                'title' => 'Mini projeto em dupla',
                'description' => 'Planeje a entrega do prototipo com sua dupla e registre a evolucao.',
                'deadline_label' => 'Entrega na proxima semana',
                'deadline_group' => 'Proximas',
                'status' => 'neutral',
            ],
        ];
    }

    private function classroomRanking(mixed $classroom, User $viewer): array
    {
        if (! $classroom) {
            return [];
        }

        return $classroom->students
            ->map(function (User $classmate) use ($classroom, $viewer) {
                $classmateClassroom = $classmate->classrooms->firstWhere('id', $classroom->id) ?? $classroom;
                $classmateScore = $this->mockedScore($classmate, $classmateClassroom);

                return $this->rankingEntry(
                    student: $classmate,
                    score: $classmateScore['student_points'],
                    href: route('student.classmates.show', $classmate, absolute: false),
                    viewer: $viewer,
                    classroomName: $classmateClassroom?->name
                );
            })
            ->sortByDesc('score')
            ->values()
            ->map(fn (array $entry, int $index) => [
                ...$entry,
                'ranking_position' => $index + 1,
            ])
            ->all();
    }

    private function pointRanking(?PointOfSchool $point, User $viewer): array
    {
        if (! $point) {
            return [];
        }

        return User::query()
            ->with([
                'role:id,name,label',
                'pointOfSchools:id,name',
                'classrooms:id,name,code,point_of_school_id,teacher_id',
            ])
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->whereHas('pointOfSchools', fn ($query) => $query->where('point_of_schools.id', $point->id))
            ->get()
            ->map(function (User $rankedStudent) use ($viewer, $point) {
                $rankedClassroom = $rankedStudent->classrooms->firstWhere('point_of_school_id', $point->id) ?? $rankedStudent->classrooms->first();
                $rankedScore = $this->mockedScore($rankedStudent, $rankedClassroom);

                return $this->rankingEntry(
                    student: $rankedStudent,
                    score: $rankedScore['student_points'],
                    href: route('student.classmates.show', $rankedStudent, absolute: false),
                    viewer: $viewer,
                    classroomName: $rankedClassroom?->name
                );
            })
            ->sortByDesc('score')
            ->values()
            ->map(fn (array $entry, int $index) => [
                ...$entry,
                'ranking_position' => $index + 1,
            ])
            ->all();
    }

    private function schoolRanking(User $viewer, ?string $search = null, ?int $classroomId = null, ?int $unitId = null): array
    {
        if (! $viewer->school_id) {
            return [];
        }

        return User::query()
            ->with([
                'role:id,name,label',
                'pointOfSchools:id,name',
                'classrooms:id,name,code,point_of_school_id,teacher_id',
            ])
            ->where('school_id', $viewer->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->when($search, function ($query, string $term) {
                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($classroomId, fn ($query, int $id) => $query->whereHas('classrooms', fn ($nested) => $nested->where('classrooms.id', $id)))
            ->when($unitId, fn ($query, int $id) => $query->whereHas('pointOfSchools', fn ($nested) => $nested->where('point_of_schools.id', $id)))
            ->get()
            ->map(function (User $rankedStudent) use ($viewer) {
                $rankedClassroom = $rankedStudent->classrooms->first();
                $rankedPoint = $rankedStudent->pointOfSchools->first();
                $rankedScore = $this->mockedScore($rankedStudent, $rankedClassroom);

                return $this->rankingEntry(
                    student: $rankedStudent,
                    score: $rankedScore['student_points'],
                    href: route('student.classmates.show', $rankedStudent, absolute: false),
                    viewer: $viewer,
                    classroomName: $rankedClassroom?->name,
                    unitName: $rankedPoint?->name
                );
            })
            ->sortByDesc('score')
            ->values()
            ->map(fn (array $entry, int $index) => [
                ...$entry,
                'ranking_position' => $index + 1,
            ])
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     score: int,
     *     ranking_position?: int,
     *     href: string,
     *     is_current_user: bool,
     *     classroom_name: string,
     *     unit_name: string
     * }
     */
    private function rankingEntry(User $student, int $score, string $href, User $viewer, ?string $classroomName, ?string $unitName = null): array
    {
        return [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'score' => $score,
            'href' => $href,
            'is_current_user' => $student->is($viewer),
            'classroom_name' => $classroomName ?? 'Sala em configuracao',
            'unit_name' => $unitName ?? ($student->pointOfSchools->first()?->name ?? 'Unidade em configuracao'),
        ];
    }

    private function schoolClassroomOptions(User $viewer): array
    {
        if (! $viewer->school_id) {
            return [];
        }

        return User::query()
            ->where('school_id', $viewer->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->with('classrooms:id,name')
            ->get()
            ->flatMap(fn (User $student) => $student->classrooms->map(fn ($classroom) => [
                'id' => $classroom->id,
                'name' => $classroom->name,
            ]))
            ->unique('id')
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function schoolUnitOptions(User $viewer): array
    {
        if (! $viewer->school_id) {
            return [];
        }

        return User::query()
            ->where('school_id', $viewer->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->with('pointOfSchools:id,name')
            ->get()
            ->flatMap(fn (User $student) => $student->pointOfSchools->map(fn ($unit) => [
                'id' => $unit->id,
                'name' => $unit->name,
            ]))
            ->unique('id')
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
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
}
