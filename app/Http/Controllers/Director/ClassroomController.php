<?php

namespace App\Http\Controllers\Director;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Director\ClassroomIndexRequest;
use App\Http\Requests\Director\StoreClassroomRequest;
use App\Http\Requests\Director\UpdateClassroomRequest;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\User;
use App\Support\PointOfSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassroomController extends Controller
{
    public function index(ClassroomIndexRequest $request): Response
    {
        $director = $request->user();
        $filters = $request->validated();
        $pointIds = PointOfSchoolContext::selectedPointIds($request, $director);

        $classroomsQuery = Classroom::query()
            ->with(['pointOfSchool:id,name', 'teacher:id,name', 'students:id,name'])
            ->withCount('students')
            ->where('school_id', $director?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(fn ($searchQuery) => $searchQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status));

        $classrooms = $classroomsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Classroom $classroom) => [
                'id' => $classroom->id,
                'point_of_school_id' => $classroom->point_of_school_id,
                'teacher_id' => $classroom->teacher_id,
                'name' => $classroom->name,
                'code' => $classroom->code,
                'status' => $classroom->status,
                'point_of_school' => $classroom->pointOfSchool?->name,
                'teacher' => $classroom->teacher?->name ?? '-',
                'students_count' => $classroom->students_count,
                'student_ids' => $classroom->students->pluck('id')->values()->all(),
            ]);

        return Inertia::render('director/Classrooms/Index', [
            'stats' => [
                'total' => Classroom::query()
                    ->where('school_id', $director?->school_id)
                    ->whereIn('point_of_school_id', $pointIds)
                    ->count(),
                'active' => Classroom::query()
                    ->where('school_id', $director?->school_id)
                    ->where('status', 'active')
                    ->whereIn('point_of_school_id', $pointIds)
                    ->count(),
                'students' => Classroom::query()
                    ->withCount('students')
                    ->where('school_id', $director?->school_id)
                    ->whereIn('point_of_school_id', $pointIds)
                    ->get()
                    ->sum('students_count'),
            ],
            'classrooms' => $classrooms,
            'filters' => $filters,
        ]);
    }

    public function create(ClassroomIndexRequest $request): Response
    {
        return Inertia::render('director/Classrooms/Form', [
            'classroom' => null,
            ...$this->formPayload($request->user()),
        ]);
    }

    public function edit(Request $request, Classroom $classroom): Response
    {
        abort_unless($this->canManageClassroom($request->user(), $classroom), 404);

        return Inertia::render('director/Classrooms/Form', [
            'classroom' => [
                'id' => $classroom->id,
                'point_of_school_id' => $classroom->point_of_school_id,
                'teacher_id' => $classroom->teacher_id,
                'name' => $classroom->name,
                'code' => $classroom->code,
                'status' => $classroom->status,
                'student_ids' => $classroom->students()->pluck('users.id')->values()->all(),
            ],
            ...$this->formPayload($request->user(), $classroom),
        ]);
    }

    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        $director = $request->user();
        $data = $request->validated();
        $studentIds = $data['student_ids'] ?? [];
        unset($data['student_ids']);
        $data['school_id'] = $director?->school_id;

        $classroom = Classroom::query()->create($data);
        $classroom->students()->sync($studentIds);

        return to_route('director.classrooms.index')->with('success', 'Turma criada com sucesso.');
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        abort_unless($this->canManageClassroom($request->user(), $classroom), 404);

        $data = $request->validated();
        $studentIds = $data['student_ids'] ?? [];
        unset($data['student_ids']);

        $classroom->update($data);
        $classroom->students()->sync($studentIds);

        return to_route('director.classrooms.index')->with('success', 'Turma atualizada com sucesso.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        abort_unless($this->canManageClassroom(request()->user(), $classroom), 404);

        $classroom->students()->detach();
        $classroom->delete();

        return to_route('director.classrooms.index')->with('success', 'Turma removida com sucesso.');
    }

    private function canManageClassroom(?User $director, Classroom $classroom): bool
    {
        if (! $director || $classroom->school_id !== $director->school_id) {
            return false;
        }

        return $director->pointOfSchools()->where('point_of_schools.id', $classroom->point_of_school_id)->exists();
    }

    private function formPayload(?User $director, ?Classroom $currentClassroom = null): array
    {
        $pointIds = $director?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        $teachers = User::query()
            ->where('school_id', $director?->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::TEACHER->value))
            ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
            ->orderBy('name')
            ->get()
            ->map(fn (User $teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'point_of_school_ids' => $teacher->pointOfSchools()->pluck('point_of_schools.id')->values()->all(),
            ]);

        $students = User::query()
            ->with(['classrooms:id,name'])
            ->where('school_id', $director?->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
            ->orderBy('name')
            ->get()
            ->map(fn (User $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'point_of_school_ids' => $student->pointOfSchools()->pluck('point_of_schools.id')->values()->all(),
                'assigned_classroom_id' => $student->classrooms->first()?->id,
                'assigned_classroom_name' => $student->classrooms->first()?->name,
                'is_assigned_to_current_classroom' => $currentClassroom ? $student->classrooms->contains('id', $currentClassroom->id) : false,
            ]);

        return [
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
            'teachers' => $teachers,
            'students' => $students,
        ];
    }
}
