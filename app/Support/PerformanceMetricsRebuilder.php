<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\Classroom;
use App\Models\School;
use App\Models\StudentClassroomPerformance;
use App\Models\TeacherClassroomMetric;
use App\Models\TeacherMonthlyMetric;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PerformanceMetricsRebuilder
{
    public function rebuildAll(): void
    {
        School::query()
            ->pluck('id')
            ->each(fn (int $schoolId) => $this->rebuildSchool($schoolId));
    }

    public function rebuildSchool(int $schoolId): void
    {
        $classrooms = Classroom::query()
            ->with([
                'teacher:id,name',
                'students:id,name',
            ])
            ->where('school_id', $schoolId)
            ->get(['id', 'school_id', 'point_of_school_id', 'teacher_id', 'name', 'code', 'status']);

        $classroomIds = $classrooms->pluck('id');

        $activities = Activity::query()
            ->with([
                'submissions:id,activity_id,student_id,classroom_id,submitted_at,score,total_points,correct_answers_count',
            ])
            ->whereIn('classroom_id', $classroomIds)
            ->get([
                'id',
                'classroom_id',
                'teacher_id',
                'title',
                'status',
                'questions_count',
                'total_points',
                'due_date',
                'created_at',
            ]);

        DB::transaction(function () use ($schoolId, $classrooms, $activities): void {
            $studentPerformances = $this->studentPerformanceRows($schoolId, $classrooms, $activities);
            StudentClassroomPerformance::query()->where('school_id', $schoolId)->delete();

            if ($studentPerformances->isNotEmpty()) {
                StudentClassroomPerformance::query()->insert($studentPerformances->all());
            }

            $this->recalculateRanks($schoolId);

            $teacherClassroomMetrics = $this->teacherClassroomMetricRows($schoolId, $classrooms, $activities);
            TeacherClassroomMetric::query()->where('school_id', $schoolId)->delete();

            if ($teacherClassroomMetrics->isNotEmpty()) {
                TeacherClassroomMetric::query()->insert($teacherClassroomMetrics->all());
            }

            $teacherMonthlyMetrics = $this->teacherMonthlyMetricRows($schoolId, $classrooms, $activities);
            TeacherMonthlyMetric::query()->where('school_id', $schoolId)->delete();

            if ($teacherMonthlyMetrics->isNotEmpty()) {
                TeacherMonthlyMetric::query()->insert($teacherMonthlyMetrics->all());
            }
        });
    }

    public function rebuildTeacher(int $teacherId): void
    {
        $schoolId = User::query()->whereKey($teacherId)->value('school_id');

        if ($schoolId) {
            $this->rebuildSchool((int) $schoolId);
        }
    }

    public function rebuildClassroom(int $classroomId): void
    {
        $schoolId = Classroom::query()->whereKey($classroomId)->value('school_id');

        if ($schoolId) {
            $this->rebuildSchool((int) $schoolId);
        }
    }

    private function studentPerformanceRows(int $schoolId, Collection $classrooms, Collection $activities): Collection
    {
        $now = now();
        $activitiesByClassroom = $activities->groupBy('classroom_id');

        return $classrooms
            ->flatMap(function (Classroom $classroom) use ($schoolId, $activitiesByClassroom, $now) {
                $classroomActivities = $activitiesByClassroom->get($classroom->id, collect());

                $submissionRowsByStudent = $classroomActivities
                    ->flatMap(fn (Activity $activity) => $activity->submissions->map(fn (ActivitySubmission $submission) => [
                        'submission' => $submission,
                        'questions_count' => $activity->questions_count,
                    ]))
                    ->groupBy(fn (array $row) => $row['submission']->student_id);

                return $classroom->students->map(function (User $student) use ($classroom, $schoolId, $submissionRowsByStudent, $now) {
                    $studentRows = collect($submissionRowsByStudent->get($student->id, []));
                    $submittedActivitiesCount = $studentRows->count();
                    $totalScore = round((float) $studentRows->sum(fn (array $row) => (float) $row['submission']->score), 2);
                    $totalPossibleScore = round((float) $studentRows->sum(fn (array $row) => (float) $row['submission']->total_points), 2);
                    $correctAnswersCount = (int) $studentRows->sum(fn (array $row) => (int) $row['submission']->correct_answers_count);
                    $answeredQuestionsCount = (int) $studentRows->sum(fn (array $row) => (int) $row['questions_count']);
                    $accuracyRate = $answeredQuestionsCount > 0 ? round(($correctAnswersCount / $answeredQuestionsCount) * 100, 2) : 0;
                    $performanceRate = $totalPossibleScore > 0 ? round(($totalScore / $totalPossibleScore) * 100, 2) : 0;
                    $lastSubmittedAt = $studentRows->max(fn (array $row) => $row['submission']->submitted_at);

                    return [
                        'student_id' => $student->id,
                        'classroom_id' => $classroom->id,
                        'point_of_school_id' => $classroom->point_of_school_id,
                        'school_id' => $schoolId,
                        'total_score' => $totalScore,
                        'total_possible_score' => $totalPossibleScore,
                        'submitted_activities_count' => $submittedActivitiesCount,
                        'correct_answers_count' => $correctAnswersCount,
                        'answered_questions_count' => $answeredQuestionsCount,
                        'accuracy_rate' => $accuracyRate,
                        'performance_rate' => $performanceRate,
                        'classroom_rank' => 0,
                        'point_rank' => 0,
                        'school_rank' => 0,
                        'last_submitted_at' => $lastSubmittedAt,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                });
            })
            ->values();
    }

    private function teacherClassroomMetricRows(int $schoolId, Collection $classrooms, Collection $activities): Collection
    {
        $now = now();
        $activitiesByClassroom = $activities->groupBy('classroom_id');
        $performancesByClassroom = StudentClassroomPerformance::query()
            ->with('student:id,name')
            ->where('school_id', $schoolId)
            ->get()
            ->groupBy('classroom_id');

        return $classrooms
            ->filter(fn (Classroom $classroom) => $classroom->teacher_id !== null)
            ->map(function (Classroom $classroom) use ($schoolId, $activitiesByClassroom, $performancesByClassroom, $now) {
                $classroomActivities = $activitiesByClassroom->get($classroom->id, collect());
                $studentsCount = $classroom->students->count();
                $publishedActivities = $classroomActivities->where('status', 'published');
                $draftActivities = $classroomActivities->where('status', 'draft');
                $submittedSubmissionsCount = (int) $classroomActivities->sum(fn (Activity $activity) => $activity->submissions->count());
                $expectedSubmissionsCount = (int) ($studentsCount * $publishedActivities->count());
                $pendingSubmissionsCount = max(0, $expectedSubmissionsCount - $submittedSubmissionsCount);
                $totalScoreEarned = round((float) $classroomActivities->sum(fn (Activity $activity) => $activity->submissions->sum('score')), 2);
                $totalScorePossible = round((float) $classroomActivities->sum(fn (Activity $activity) => $activity->submissions->sum('total_points')), 2);
                $averageCompletionRate = $expectedSubmissionsCount > 0 ? round(($submittedSubmissionsCount / $expectedSubmissionsCount) * 100, 2) : 0;
                $averagePerformanceRate = $totalScorePossible > 0 ? round(($totalScoreEarned / $totalScorePossible) * 100, 2) : 0;
                $topStudent = $this->sortPerformances($performancesByClassroom->get($classroom->id, collect()))->first();

                return [
                    'teacher_id' => $classroom->teacher_id,
                    'classroom_id' => $classroom->id,
                    'point_of_school_id' => $classroom->point_of_school_id,
                    'school_id' => $schoolId,
                    'top_student_id' => $topStudent?->student_id,
                    'classroom_status' => $classroom->status,
                    'students_count' => $studentsCount,
                    'total_activities_count' => $classroomActivities->count(),
                    'published_activities_count' => $publishedActivities->count(),
                    'draft_activities_count' => $draftActivities->count(),
                    'total_questions_count' => (int) $classroomActivities->sum('questions_count'),
                    'available_points' => round((float) $classroomActivities->sum('total_points'), 2),
                    'expected_submissions_count' => $expectedSubmissionsCount,
                    'submitted_submissions_count' => $submittedSubmissionsCount,
                    'pending_submissions_count' => $pendingSubmissionsCount,
                    'total_score_earned' => $totalScoreEarned,
                    'total_score_possible' => $totalScorePossible,
                    'average_completion_rate' => $averageCompletionRate,
                    'average_performance_rate' => $averagePerformanceRate,
                    'top_student_score' => round((float) ($topStudent?->total_score ?? 0), 2),
                    'top_student_accuracy_rate' => round((float) ($topStudent?->accuracy_rate ?? 0), 2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values();
    }

    private function teacherMonthlyMetricRows(int $schoolId, Collection $classrooms, Collection $activities): Collection
    {
        $now = now();
        $activitiesByClassroom = $activities->groupBy('classroom_id');
        $months = collect(range(5, 0))->map(fn (int $monthsAgo) => now()->startOfMonth()->subMonths($monthsAgo));
        $teacherPointPairs = $classrooms
            ->filter(fn (Classroom $classroom) => $classroom->teacher_id !== null)
            ->map(fn (Classroom $classroom) => [
                'teacher_id' => $classroom->teacher_id,
                'point_of_school_id' => $classroom->point_of_school_id,
            ])
            ->unique(fn (array $pair) => $pair['teacher_id'].'-'.$pair['point_of_school_id'])
            ->values();

        return $teacherPointPairs
            ->flatMap(function (array $pair) use ($months, $schoolId, $classrooms, $activitiesByClassroom, $now) {
                $teacherClassrooms = $classrooms
                    ->where('teacher_id', $pair['teacher_id'])
                    ->where('point_of_school_id', $pair['point_of_school_id'])
                    ->values();

                $teacherActivities = $teacherClassrooms
                    ->flatMap(fn (Classroom $classroom) => $activitiesByClassroom->get($classroom->id, collect()))
                    ->values();

                return $months->map(function (CarbonInterface $month) use ($pair, $teacherClassrooms, $teacherActivities, $schoolId, $now) {
                    $bucketActivities = $teacherActivities
                        ->filter(fn (Activity $activity) => $this->bucketDate($activity)?->isSameMonth($month))
                        ->values();
                    $publishedBucketActivities = $bucketActivities->where('status', 'published');
                    $createdActivitiesCount = $teacherActivities->filter(fn (Activity $activity) => $activity->created_at?->isSameMonth($month))->count();
                    $expectedSubmissionsCount = (int) $publishedBucketActivities->sum(function (Activity $activity) use ($teacherClassrooms) {
                        $studentsCount = $teacherClassrooms->firstWhere('id', $activity->classroom_id)?->students->count() ?? 0;

                        return $studentsCount;
                    });
                    $submittedSubmissionsCount = (int) $publishedBucketActivities->sum(fn (Activity $activity) => $activity->submissions->count());
                    $pendingSubmissionsCount = max(0, $expectedSubmissionsCount - $submittedSubmissionsCount);
                    $totalScoreEarned = round((float) $publishedBucketActivities->sum(fn (Activity $activity) => $activity->submissions->sum('score')), 2);
                    $totalScorePossible = round((float) $publishedBucketActivities->sum(fn (Activity $activity) => $activity->submissions->sum('total_points')), 2);
                    $averagePerformanceRate = $totalScorePossible > 0 ? round(($totalScoreEarned / $totalScorePossible) * 100, 2) : 0;

                    return [
                        'teacher_id' => $pair['teacher_id'],
                        'point_of_school_id' => $pair['point_of_school_id'],
                        'school_id' => $schoolId,
                        'metric_month' => $month->toDateString(),
                        'created_activities_count' => $createdActivitiesCount,
                        'expected_submissions_count' => $expectedSubmissionsCount,
                        'submitted_submissions_count' => $submittedSubmissionsCount,
                        'pending_submissions_count' => $pendingSubmissionsCount,
                        'total_score_earned' => $totalScoreEarned,
                        'total_score_possible' => $totalScorePossible,
                        'average_performance_rate' => $averagePerformanceRate,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                });
            })
            ->values();
    }

    private function recalculateRanks(int $schoolId): void
    {
        $performances = StudentClassroomPerformance::query()
            ->with('student:id,name')
            ->where('school_id', $schoolId)
            ->get();

        $this->applyRank($performances->groupBy('classroom_id'), 'classroom_rank');
        $this->applyRank($performances->groupBy('point_of_school_id'), 'point_rank');
        $this->applyRank(collect(['school' => $performances]), 'school_rank');
    }

    private function applyRank(Collection $groups, string $column): void
    {
        $groups->each(function (Collection $group) use ($column): void {
            $sortedGroup = $this->sortPerformances($group)->values();

            $sortedGroup->each(function (StudentClassroomPerformance $performance, int $index) use ($column): void {
                StudentClassroomPerformance::query()
                    ->whereKey($performance->id)
                    ->update([$column => $index + 1]);
            });
        });
    }

    private function sortPerformances(Collection $performances): Collection
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

    private function bucketDate(Activity $activity): ?CarbonInterface
    {
        return $activity->due_date ?? $activity->created_at;
    }
}
