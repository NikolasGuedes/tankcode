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
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClassroomController extends Controller
{
    public function index(ClassroomIndexRequest $request): Response
    {
        $director = $request->user();
        $filters = $request->validated();
        $pointIds = $director?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        $classrooms = Classroom::query()
            ->with(['pointOfSchool:id,name', 'teacher:id,name', 'students:id,name'])
            ->withCount('students')
            ->where('school_id', $director?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(fn ($searchQuery) => $searchQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['point_of_school_id'] ?? null, fn ($query, int $pointId) => $query->where('point_of_school_id', $pointId))
            ->latest()
            ->get()
            ->map(fn (Classroom $classroom) => [
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
            ->where('school_id', $director?->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
            ->orderBy('name')
            ->get()
            ->map(fn (User $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'point_of_school_ids' => $student->pointOfSchools()->pluck('point_of_schools.id')->values()->all(),
            ]);

        return Inertia::render('director/Classrooms/Index', [
            'stats' => [
                'total' => $classrooms->count(),
                'active' => $classrooms->where('status', 'active')->count(),
                'students' => $classrooms->sum('students_count'),
            ],
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
            'teachers' => $teachers,
            'students' => $students,
            'classrooms' => $classrooms,
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
}
