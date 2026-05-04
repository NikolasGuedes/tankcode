<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\ActivityLevelEnum;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Classroom;
use App\Models\StudentClassroomPerformance;
use App\Models\TeacherClassroomMetric;
use App\Models\TeacherMonthlyMetric;
use App\Support\PointOfSchoolContext;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $request = request();
        $teacher = $request->user();
        $pointIds = PointOfSchoolContext::selectedPointIds($request, $teacher);
        $classrooms = Classroom::query()
            ->with(['students:id,name'])
            ->withCount('students')
            ->where('teacher_id', $teacher?->id)
            ->where('school_id', $teacher?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->get();
        $classroomIds = $classrooms->pluck('id');

        $activitiesQuery = Activity::query()
            ->where('teacher_id', $teacher?->id)
            ->whereIn('classroom_id', $classroomIds);
        $activities = (clone $activitiesQuery)
            ->with('classroom:id,name')
            ->latest()
            ->get();
        $classroomMetrics = TeacherClassroomMetric::query()
            ->with(['classroom:id,name,code,status', 'topStudent:id,name'])
            ->where('teacher_id', $teacher?->id)
            ->whereIn('classroom_id', $classroomIds)
            ->whereIn('point_of_school_id', $pointIds)
            ->get();
        $studentPerformances = StudentClassroomPerformance::query()
            ->with(['student:id,name'])
            ->whereIn('classroom_id', $classroomIds)
            ->get();
        $monthlyMetrics = TeacherMonthlyMetric::query()
            ->where('teacher_id', $teacher?->id)
            ->whereIn('point_of_school_id', $pointIds)
            ->orderBy('metric_month')
            ->get();
        $studentsCount = (int) $classrooms->sum('students_count');
        $availablePoints = (float) $activities->sum('total_points');

        $realMetrics = [
            'points' => $pointIds->count(),
            'classrooms' => $classrooms->count(),
            'students' => $studentsCount,
            'activities' => $activities->count(),
            'publishedActivities' => $activities->where('status', 'published')->count(),
            'draftActivities' => $activities->where('status', 'draft')->count(),
            'totalQuestions' => (int) $activities->sum('questions_count'),
            'availablePoints' => $availablePoints,
            'averagePointsPerActivity' => $activities->count() > 0 ? round($availablePoints / $activities->count(), 2) : 0,
        ];

        $classroomActivityStats = $activities
            ->groupBy('classroom_id')
            ->map(fn (Collection $classroomActivities) => [
                'activities' => $classroomActivities->count(),
                'points' => round((float) $classroomActivities->sum('total_points'), 2),
            ]);

        $classroomMetricsByClassroom = $classroomMetrics->keyBy('classroom_id');
        $classroomSummary = $classrooms
            ->map(fn (Classroom $classroom) => [
                'id' => $classroom->id,
                'name' => $classroom->name,
                'code' => $classroom->code,
                'students' => $classroom->students_count,
                'activities' => $classroomActivityStats->get($classroom->id)['activities'] ?? 0,
                'availablePoints' => $classroomActivityStats->get($classroom->id)['points'] ?? 0,
                'submitted' => (int) ($classroomMetricsByClassroom->get($classroom->id)?->submitted_submissions_count ?? 0),
                'pending' => (int) ($classroomMetricsByClassroom->get($classroom->id)?->pending_submissions_count ?? 0),
                'completionRate' => round((float) ($classroomMetricsByClassroom->get($classroom->id)?->average_completion_rate ?? 0), 2),
                'performanceRate' => round((float) ($classroomMetricsByClassroom->get($classroom->id)?->average_performance_rate ?? 0), 2),
                'status' => $classroom->status,
            ])
            ->values();

        $charts = [
            'activityStatus' => [
                ['label' => 'Publicadas', 'value' => $realMetrics['publishedActivities'], 'color' => '#34d399'],
                ['label' => 'Rascunhos', 'value' => $realMetrics['draftActivities'], 'color' => '#a78bfa'],
            ],
            'activityDifficulty' => collect(ActivityLevelEnum::cases())
                ->map(fn (ActivityLevelEnum $level) => [
                    'label' => $level->label(),
                    'value' => $activities->filter(fn (Activity $activity) => $activity->level === $level)->count(),
                    'color' => match ($level) {
                        ActivityLevelEnum::EASY => '#34d399',
                        ActivityLevelEnum::MEDIUM => '#fbbf24',
                        ActivityLevelEnum::HARD => '#fb7185',
                    },
                ])
                ->values(),
            'activitiesByClassroom' => $classroomSummary
                ->map(fn (array $classroom) => [
                    'label' => $classroom['name'],
                    'value' => $classroom['activities'],
                    'color' => '#8b5cf6',
                ])
                ->values(),
            'studentsByClassroom' => $classroomSummary
                ->map(fn (array $classroom) => [
                    'label' => $classroom['name'],
                    'value' => $classroom['students'],
                    'color' => '#38bdf8',
                ])
                ->values(),
            'monthlyActivities' => $this->monthlyActivityEvolution($activities),
        ];
        $performanceMetrics = $this->performanceMetrics($classroomMetrics, $studentPerformances, $monthlyMetrics);

        return Inertia::render('teacher/Dashboard', [
            'realMetrics' => $realMetrics,
            'performanceMetrics' => $performanceMetrics,
            'charts' => $charts,
            'classroomSummary' => $classroomSummary,
            'upcomingActivities' => $activities
                ->filter(fn (Activity $activity) => $activity->due_date && $activity->due_date->isFuture())
                ->sortBy('due_date')
                ->take(6)
                ->map(fn (Activity $activity) => [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'classroom' => $activity->classroom?->name ?? '-',
                    'level' => $activity->level->value,
                    'levelLabel' => $activity->level->label(),
                    'dueDate' => $activity->due_date?->format('d/m/Y'),
                    'status' => $activity->status,
                    'totalPoints' => $activity->total_points,
                ])
                ->values(),
            'insights' => $this->realInsights($activities, $classroomSummary, $performanceMetrics),
        ]);
    }

    private function monthlyActivityEvolution(Collection $activities): array
    {
        return collect(range(5, 0))
            ->map(function (int $monthsAgo) use ($activities) {
                $month = now()->subMonths($monthsAgo);

                return [
                    'label' => ucfirst($month->translatedFormat('M')),
                    'value' => $activities
                        ->filter(fn (Activity $activity) => $activity->created_at?->isSameMonth($month))
                        ->count(),
                ];
            })
            ->values()
            ->all();
    }

    private function realInsights(Collection $activities, Collection $classroomSummary, array $performanceMetrics): array
    {
        if ($activities->isEmpty()) {
            return [
                'Crie sua primeira atividade para visualizar tendencias reais.',
                'Quando houver atividades, este painel mostrara distribuicao por sala e dificuldade.',
            ];
        }

        $topClassroom = $classroomSummary->sortByDesc('activities')->first();
        $topLevel = collect(ActivityLevelEnum::cases())
            ->map(fn (ActivityLevelEnum $level) => [
                'label' => $level->label(),
                'count' => $activities->filter(fn (Activity $activity) => $activity->level === $level)->count(),
            ])
            ->sortByDesc('count')
            ->first();

        return [
            "Voce possui {$activities->where('status', 'draft')->count()} atividade(s) em rascunho.",
            $topClassroom && $topClassroom['activities'] > 0
                ? "A turma com mais atividades e {$topClassroom['name']}."
                : 'Nenhuma turma possui atividades cadastradas ainda.',
            $performanceMetrics['submittedActivities'] > 0
                ? "O aproveitamento médio atual está em {$performanceMetrics['averagePerformance']}%."
                : 'As métricas de desempenho aparecerão assim que os alunos enviarem respostas.',
            $topLevel && $topLevel['count'] > 0
                ? "A dificuldade mais usada e {$topLevel['label']}."
                : 'Ainda nao ha dificuldade predominante.',
        ];
    }

    private function performanceMetrics(
        Collection $classroomMetrics,
        Collection $studentPerformances,
        Collection $monthlyMetrics,
    ): array {
        $expectedSubmissions = (int) $classroomMetrics->sum('expected_submissions_count');
        $submittedActivities = (int) $classroomMetrics->sum('submitted_submissions_count');
        $pendingSubmissions = (int) $classroomMetrics->sum('pending_submissions_count');
        $totalScoreEarned = (float) $classroomMetrics->sum('total_score_earned');
        $totalScorePossible = (float) $classroomMetrics->sum('total_score_possible');
        $averageCompletionRate = $expectedSubmissions > 0 ? round(($submittedActivities / $expectedSubmissions) * 100, 2) : 0;
        $averagePerformance = $totalScorePossible > 0 ? round(($totalScoreEarned / $totalScorePossible) * 100, 2) : 0;
        $topStudentPerformance = $this->sortStudentPerformances($studentPerformances)->first();
        $topClassroomMetric = $classroomMetrics
            ->sort(function (TeacherClassroomMetric $left, TeacherClassroomMetric $right) {
                $performanceComparison = (float) $right->average_performance_rate <=> (float) $left->average_performance_rate;

                if ($performanceComparison !== 0) {
                    return $performanceComparison;
                }

                $completionComparison = (float) $right->average_completion_rate <=> (float) $left->average_completion_rate;

                if ($completionComparison !== 0) {
                    return $completionComparison;
                }

                return strcasecmp($left->classroom?->name ?? '', $right->classroom?->name ?? '');
            })
            ->first();

        return [
            'averageCompletionRate' => $averageCompletionRate,
            'averagePerformance' => $averagePerformance,
            'submittedActivities' => $submittedActivities,
            'pendingSubmissions' => $pendingSubmissions,
            'completedSubmissions' => $submittedActivities,
            'topStudent' => $topStudentPerformance?->student?->name,
            'topClassroom' => $topClassroomMetric?->classroom?->name,
            'topStudents' => $this->sortStudentPerformances($studentPerformances)
                ->take(6)
                ->values()
                ->map(fn (StudentClassroomPerformance $performance) => [
                    'name' => $performance->student?->name ?? 'Aluno',
                    'points' => round((float) $performance->total_score, 2),
                    'performance' => round((float) $performance->performance_rate, 2),
                ]),
            'classroomPerformance' => $classroomMetrics
                ->sortByDesc('average_performance_rate')
                ->take(6)
                ->values()
                ->map(fn (TeacherClassroomMetric $metric) => [
                    'name' => $metric->classroom?->name ?? 'Turma',
                    'performance' => round((float) $metric->average_performance_rate, 2),
                    'completed' => (int) $metric->submitted_submissions_count,
                    'pending' => (int) $metric->pending_submissions_count,
                ]),
            'monthlySubmissions' => collect(range(5, 0))
                ->map(function (int $monthsAgo) use ($monthlyMetrics) {
                    $month = now()->startOfMonth()->subMonths($monthsAgo);
                    $bucket = $monthlyMetrics->filter(fn (TeacherMonthlyMetric $metric) => $metric->metric_month?->isSameMonth($month));

                    return [
                        'label' => ucfirst($month->translatedFormat('M')),
                        'submitted' => (int) $bucket->sum('submitted_submissions_count'),
                        'pending' => (int) $bucket->sum('pending_submissions_count'),
                    ];
                }),
            'performanceDistribution' => $this->performanceDistribution($studentPerformances),
        ];
    }

    private function performanceDistribution(Collection $studentPerformances): array
    {
        $totalStudents = max(1, $studentPerformances->count());

        return collect([
            ['label' => 'Excelente', 'count' => $studentPerformances->where('performance_rate', '>=', 90)->count(), 'color' => '#34d399'],
            ['label' => 'Bom', 'count' => $studentPerformances->filter(fn (StudentClassroomPerformance $performance) => (float) $performance->performance_rate >= 70 && (float) $performance->performance_rate < 90)->count(), 'color' => '#8b5cf6'],
            ['label' => 'Regular', 'count' => $studentPerformances->filter(fn (StudentClassroomPerformance $performance) => (float) $performance->performance_rate >= 50 && (float) $performance->performance_rate < 70)->count(), 'color' => '#fbbf24'],
            ['label' => 'Baixo', 'count' => $studentPerformances->where('performance_rate', '<', 50)->count(), 'color' => '#fb7185'],
        ])
            ->map(fn (array $bucket) => [
                'label' => $bucket['label'],
                'value' => round(($bucket['count'] / $totalStudents) * 100, 2),
                'color' => $bucket['color'],
            ])
            ->all();
    }

    private function sortStudentPerformances(Collection $performances): Collection
    {
        return $performances->sort(function (StudentClassroomPerformance $left, StudentClassroomPerformance $right) {
            $scoreComparison = (float) $right->total_score <=> (float) $left->total_score;

            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            $accuracyComparison = (float) $right->accuracy_rate <=> (float) $left->accuracy_rate;

            if ($accuracyComparison !== 0) {
                return $accuracyComparison;
            }

            return strcasecmp($left->student?->name ?? '', $right->student?->name ?? '');
        });
    }
}
