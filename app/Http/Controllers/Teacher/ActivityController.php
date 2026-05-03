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
use Illuminate\Support\Facades\DB;
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
            ->where(fn ($query) => $query
                ->where('teacher_id', $teacher?->id)
                ->orWhereIn('classroom_id', $classroomIds));

        $activities = (clone $baseActivitiesQuery)
            ->with(['classroom:id,name,code,point_of_school_id', 'questions'])
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
                'questions' => $activity->questions->map(fn ($question) => [
                    'id' => $question->id,
                    'type' => $question->type,
                    'statement' => $question->statement,
                    'correct_option' => $question->correct_option,
                    'options' => $question->options,
                    'keywords' => $question->keywords,
                    'blank_answers' => $question->blank_answers,
                    'left_column' => $question->left_column,
                    'right_column' => $question->right_column,
                    'pairs' => $question->pairs,
                ])->values(),
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
        $questions = $data['questions'];
        unset($data['questions']);

        DB::transaction(function () use ($request, $calculator, $data, $questions): void {
            $questionsCount = count($questions);
            $points = $calculator->calculate($data['level'], $questionsCount);

            $activity = Activity::query()->create([
                ...$data,
                ...$points,
                'teacher_id' => $request->user()?->id,
                'questions_count' => $questionsCount,
                'status' => $data['status'] ?? 'draft',
            ]);

            $this->createQuestions($activity, $questions);
        });

        return to_route('teacher.activities.index')->with('success', 'Atividade criada com sucesso.');
    }

    public function update(UpdateActivityRequest $request, Activity $activity, ActivityPointsCalculator $calculator): RedirectResponse
    {
        abort_unless($this->canManageActivity($request->user(), $activity), 404);

        $data = $request->validated();
        $questions = $data['questions'];
        unset($data['questions']);

        DB::transaction(function () use ($request, $activity, $calculator, $data, $questions): void {
            $questionsCount = count($questions);
            $points = $calculator->calculate($data['level'], $questionsCount);

            $activity->update([
                ...$data,
                ...$points,
                'teacher_id' => $request->user()?->id,
                'questions_count' => $questionsCount,
                'status' => $data['status'] ?? 'draft',
            ]);

            $activity->questions()->delete();
            $this->createQuestions($activity, $questions);
        });

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

    private function createQuestions(Activity $activity, array $questions): void
    {
        collect($questions)
            ->values()
            ->each(fn (array $question, int $index) => $activity->questions()->create($this->questionPayload($question, $index)));
    }

    private function questionPayload(array $question, int $index): array
    {
        return [
            'type' => $question['type'],
            'statement' => $question['statement'],
            'order' => $index + 1,
            'correct_option' => $question['type'] === 'multiple_choice' ? ($question['correct_option'] ?? null) : null,
            'options' => $question['type'] === 'multiple_choice' ? ($question['options'] ?? null) : null,
            'keywords' => $question['type'] === 'drag_drop' ? array_values($question['keywords'] ?? []) : null,
            'blank_answers' => $question['type'] === 'drag_drop' ? ($question['blank_answers'] ?? null) : null,
            'left_column' => $question['type'] === 'matching' ? array_values($question['left_column'] ?? []) : null,
            'right_column' => $question['type'] === 'matching' ? array_values($question['right_column'] ?? []) : null,
            'pairs' => $question['type'] === 'matching' ? ($question['pairs'] ?? null) : null,
        ];
    }
}
