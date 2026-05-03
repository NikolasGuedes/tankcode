<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreActivityRequest;
use App\Http\Requests\Teacher\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Classroom;
use App\Models\User;
use App\Support\ActivityPointsCalculator;
use App\Support\PointOfSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $teacher = $request->user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:draft,published'],
            'classroom_id' => ['nullable', 'integer'],
        ]);
        $pointIds = PointOfSchoolContext::selectedPointIds($request, $teacher);
        $classroomIds = $this->classroomsForTeacher($teacher, $pointIds)->pluck('id');

        $baseActivitiesQuery = Activity::query()
            ->where('teacher_id', $teacher?->id)
            ->whereIn('classroom_id', $classroomIds);

        $activities = (clone $baseActivitiesQuery)
            ->with(['classroom:id,name,code,point_of_school_id'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(fn ($searchQuery) => $searchQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhereHas('classroom', fn ($classroomQuery) => $classroomQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")));
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['classroom_id'] ?? null, fn ($query, int $classroomId) => $query->where('classroom_id', $classroomId))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Activity $activity) => [
                'id' => $activity->id,
                'classroom_id' => $activity->classroom_id,
                'classroom' => $activity->classroom?->name ?? '-',
                'classroom_code' => $activity->classroom?->code,
                'title' => $activity->title,
                'description' => $activity->description,
                'level' => $activity->level->value,
                'level_label' => $activity->level->label(),
                'questions_count' => $activity->questions_count,
                'points_per_question' => $activity->points_per_question,
                'total_points' => $activity->total_points,
                'due_date' => optional($activity->due_date)?->format('Y-m-d'),
                'due_date_label' => optional($activity->due_date)?->format('d/m/Y') ?? '-',
                'status' => $activity->status,
            ]);

        return Inertia::render('teacher/Activities/Index', [
            'stats' => [
                'total' => (clone $baseActivitiesQuery)->count(),
                'published' => (clone $baseActivitiesQuery)->where('status', 'published')->count(),
                'draft' => (clone $baseActivitiesQuery)->where('status', 'draft')->count(),
                'questions' => (int) (clone $baseActivitiesQuery)->sum('questions_count'),
                'points' => (float) (clone $baseActivitiesQuery)->sum('total_points'),
            ],
            'activities' => $activities,
            'classrooms' => $this->classroomsForTeacher($teacher, $pointIds)
                ->map(fn (Classroom $classroom) => [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                    'code' => $classroom->code,
                    'point_of_school' => $classroom->pointOfSchool?->name,
                ]),
            'filters' => $filters,
        ]);
    }

    public function store(StoreActivityRequest $request, ActivityPointsCalculator $calculator): RedirectResponse
    {
        $data = $request->validated();
        $points = $calculator->calculate($data['level'], (int) $data['questions_count']);

        Activity::query()->create([
            ...$data,
            ...$points,
            'teacher_id' => $request->user()?->id,
            'status' => $data['status'] ?? 'draft',
        ]);

        return to_route('teacher.activities.index')->with('success', 'Atividade criada com sucesso.');
    }

    public function update(UpdateActivityRequest $request, Activity $activity, ActivityPointsCalculator $calculator): RedirectResponse
    {
        abort_unless($this->canManageActivity($request->user(), $activity), 404);

        $data = $request->validated();
        $points = $calculator->calculate($data['level'], (int) $data['questions_count']);

        $activity->update([
            ...$data,
            ...$points,
            'teacher_id' => $request->user()?->id,
            'status' => $data['status'] ?? 'draft',
        ]);

        return to_route('teacher.activities.index')->with('success', 'Atividade atualizada com sucesso.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        abort_unless($this->canManageActivity(request()->user(), $activity), 404);

        $activity->delete();

        return to_route('teacher.activities.index')->with('success', 'Atividade removida com sucesso.');
    }

    private function canManageActivity(?User $teacher, Activity $activity): bool
    {
        if (! $teacher) {
            return false;
        }

        $pointIds = $teacher->pointOfSchools()->pluck('point_of_schools.id');
        $classroomIsAllowed = $activity->classroom()
            ->where('teacher_id', $teacher->id)
            ->where('school_id', $teacher->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->exists();

        return $activity->teacher_id === $teacher->id || $classroomIsAllowed;
    }

    private function classroomsForTeacher(?User $teacher, $pointIds)
    {
        return Classroom::query()
            ->with('pointOfSchool:id,name')
            ->where('teacher_id', $teacher?->id)
            ->where('school_id', $teacher?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->orderBy('name')
            ->get(['id', 'point_of_school_id', 'name', 'code']);
    }
}
