<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\ActivityLevelEnum;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Classroom;
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

        $classroomSummary = $classrooms
            ->map(fn (Classroom $classroom) => [
                'id' => $classroom->id,
                'name' => $classroom->name,
                'code' => $classroom->code,
                'students' => $classroom->students_count,
                'activities' => $classroomActivityStats->get($classroom->id)['activities'] ?? 0,
                'availablePoints' => $classroomActivityStats->get($classroom->id)['points'] ?? 0,
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

        $mockMetrics = $this->mockFutureMetrics($classrooms, $studentsCount);

        return Inertia::render('teacher/Dashboard', [
            'realMetrics' => $realMetrics,
            'mockMetrics' => $mockMetrics,
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
            'insights' => $this->realInsights($activities, $classroomSummary),
            'futureMetrics' => [
                [
                    'title' => 'Entregas e respostas',
                    'description' => 'Sera substituido por consultas reais quando submissions/respostas existirem.',
                    'status' => 'Metrica futura',
                ],
                [
                    'title' => 'Pontuacao e aproveitamento',
                    'description' => 'Hoje e simulado porque ainda nao ha tabela real de scores.',
                    'status' => 'Dados simulados',
                ],
                [
                    'title' => 'Rankings de alunos',
                    'description' => 'Ranking visual preparado para a futura estrutura de desempenho.',
                    'status' => 'Dados simulados',
                ],
            ],
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

    private function realInsights(Collection $activities, Collection $classroomSummary): array
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
            $topLevel && $topLevel['count'] > 0
                ? "A dificuldade mais usada e {$topLevel['label']}."
                : 'Ainda nao ha dificuldade predominante.',
        ];
    }

    /**
     * Dados temporarios para areas que dependem de submissions, respostas e scores.
     * Substituir por queries reais quando essas tabelas forem implementadas.
     */
    private function mockFutureMetrics(Collection $classrooms, int $studentsCount): array
    {
        $students = $classrooms
            ->flatMap(fn (Classroom $classroom) => $classroom->students)
            ->unique('id')
            ->values();
        $studentNames = $students->isNotEmpty()
            ? $students->pluck('name')->values()
            : collect(['Aluno simulado 1', 'Aluno simulado 2', 'Aluno simulado 3', 'Aluno simulado 4']);
        $classroomNames = $classrooms->isNotEmpty()
            ? $classrooms->pluck('name')->values()
            : collect(['Turma modelo A', 'Turma modelo B', 'Turma modelo C']);
        $completionRate = min(92, 68 + ($classrooms->count() * 3));
        $averagePerformance = min(94, 74 + max(0, $studentsCount));
        $submittedActivities = max(18, $studentsCount * 4 + $classrooms->count() * 7);
        $pendingSubmissions = max(8, $classrooms->count() * 5 + 6);

        return [
            'averageCompletionRate' => $completionRate,
            'averagePerformance' => $averagePerformance,
            'submittedActivities' => $submittedActivities,
            'pendingSubmissions' => $pendingSubmissions,
            'completedSubmissions' => max(0, $submittedActivities - $pendingSubmissions),
            'topStudent' => $studentNames->first(),
            'topClassroom' => $classroomNames->first(),
            'topStudents' => $studentNames
                ->take(6)
                ->values()
                ->map(fn (string $name, int $index) => [
                    'name' => $name,
                    'points' => 980 - ($index * 47),
                    'performance' => max(64, 96 - ($index * 4)),
                ]),
            'classroomPerformance' => $classroomNames
                ->take(6)
                ->values()
                ->map(fn (string $name, int $index) => [
                    'name' => $name,
                    'performance' => max(58, 91 - ($index * 5)),
                    'completed' => max(8, 34 - ($index * 3)),
                    'pending' => 6 + $index,
                ]),
            'monthlySubmissions' => collect(range(5, 0))
                ->map(fn (int $monthsAgo, int $index) => [
                    'label' => ucfirst(now()->subMonths($monthsAgo)->translatedFormat('M')),
                    'submitted' => 24 + ($index * 8) + $classrooms->count(),
                    'pending' => max(5, 18 - $index),
                ]),
            'monthlyPerformance' => collect(range(5, 0))
                ->map(fn (int $monthsAgo, int $index) => [
                    'label' => ucfirst(now()->subMonths($monthsAgo)->translatedFormat('M')),
                    'value' => min(95, 72 + ($index * 4)),
                ]),
            'performanceDistribution' => [
                ['label' => 'Excelente', 'value' => 34, 'color' => '#34d399'],
                ['label' => 'Bom', 'value' => 42, 'color' => '#8b5cf6'],
                ['label' => 'Regular', 'value' => 18, 'color' => '#fbbf24'],
                ['label' => 'Baixo', 'value' => 6, 'color' => '#fb7185'],
            ],
        ];
    }
}
