<?php

namespace App\Support;

use App\Enums\RoleEnum;
use App\Models\Achievement;
use App\Models\ActivitySubmission;
use App\Models\StudentAchievement;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class StudentAchievementSyncService
{
    public function syncSchool(int $schoolId): void
    {
        $students = User::query()
            ->with($this->studentRelations())
            ->where('school_id', $schoolId)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
            ->get();

        $this->syncStudents($students);
    }

    public function syncStudent(User $student): void
    {
        $student->loadMissing($this->studentRelations());

        $this->syncStudents(collect([$student]));
    }

    /**
     * @param  Collection<int, User>  $students
     */
    public function syncStudents(Collection $students): void
    {
        if ($students->isEmpty()) {
            return;
        }

        $achievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($achievements->isEmpty()) {
            return;
        }

        $students->each(function (User $student) use ($achievements): void {
            $existingAchievementIds = $student->studentAchievements->pluck('achievement_id')->all();
            $submissions = $student->activitySubmissions
                ->filter(fn (ActivitySubmission $submission) => $submission->submitted_at !== null)
                ->sortBy(fn (ActivitySubmission $submission) => $submission->submitted_at?->getTimestamp())
                ->values();

            $achievements->each(function (Achievement $achievement) use ($student, $submissions, &$existingAchievementIds): void {
                if (in_array($achievement->id, $existingAchievementIds, true)) {
                    return;
                }

                $match = $this->matchAchievement($student, $achievement, $submissions);

                if (! $match) {
                    return;
                }

                StudentAchievement::query()->create([
                    'student_id' => $student->id,
                    'achievement_id' => $achievement->id,
                    'awarded_at' => $match['awarded_at'],
                    'criteria_snapshot' => $match['criteria_snapshot'],
                ]);

                $existingAchievementIds[] = $achievement->id;
            });
        });
    }

    /**
     * @return array<int, string|array<int, string>>
     */
    private function studentRelations(): array
    {
        return [
            'studentAchievements:id,student_id,achievement_id,awarded_at,criteria_snapshot',
            'activitySubmissions:id,activity_id,student_id,classroom_id,submitted_at,score,total_points,correct_answers_count',
            'activitySubmissions.activity:id,questions_count',
        ];
    }

    /**
     * @param  Collection<int, ActivitySubmission>  $submissions
     * @return array{awarded_at: CarbonImmutable, criteria_snapshot: array<string, mixed>}|null
     */
    private function matchAchievement(User $student, Achievement $achievement, Collection $submissions): ?array
    {
        return match ($achievement->rule_type) {
            'completed_activities_gte' => $this->matchCompletedActivities($submissions, (int) Arr::get($achievement->rule_config ?? [], 'count', 0)),
            'perfect_activity_once' => $this->matchPerfectActivity($submissions),
            'submission_streak_days' => $this->matchSubmissionStreak($submissions, (int) Arr::get($achievement->rule_config ?? [], 'days', 0)),
            'profile_completed' => $this->matchProfileCompleted($student),
            'points_gte' => $this->matchPoints($submissions, (float) Arr::get($achievement->rule_config ?? [], 'points', 0)),
            default => null,
        };
    }

    /**
     * @param  Collection<int, ActivitySubmission>  $submissions
     * @return array{awarded_at: CarbonImmutable, criteria_snapshot: array<string, mixed>}|null
     */
    private function matchCompletedActivities(Collection $submissions, int $threshold): ?array
    {
        if ($threshold <= 0 || $submissions->count() < $threshold) {
            return null;
        }

        /** @var ActivitySubmission $submission */
        $submission = $submissions->values()->get($threshold - 1);

        return [
            'awarded_at' => CarbonImmutable::parse($submission->submitted_at),
            'criteria_snapshot' => [
                'threshold' => $threshold,
                'submitted_activities_count' => $submissions->count(),
                'activity_submission_id' => $submission->id,
            ],
        ];
    }

    /**
     * @param  Collection<int, ActivitySubmission>  $submissions
     * @return array{awarded_at: CarbonImmutable, criteria_snapshot: array<string, mixed>}|null
     */
    private function matchPerfectActivity(Collection $submissions): ?array
    {
        /** @var ActivitySubmission|null $submission */
        $submission = $submissions->first(function (ActivitySubmission $submission) {
            $questionsCount = (int) ($submission->activity?->questions_count ?? 0);

            return $questionsCount > 0 && (int) $submission->correct_answers_count === $questionsCount;
        });

        if (! $submission) {
            return null;
        }

        return [
            'awarded_at' => CarbonImmutable::parse($submission->submitted_at),
            'criteria_snapshot' => [
                'activity_submission_id' => $submission->id,
                'activity_id' => $submission->activity_id,
                'correct_answers_count' => (int) $submission->correct_answers_count,
                'questions_count' => (int) ($submission->activity?->questions_count ?? 0),
            ],
        ];
    }

    /**
     * @param  Collection<int, ActivitySubmission>  $submissions
     * @return array{awarded_at: CarbonImmutable, criteria_snapshot: array<string, mixed>}|null
     */
    private function matchSubmissionStreak(Collection $submissions, int $threshold): ?array
    {
        if ($threshold <= 0) {
            return null;
        }

        $dates = $submissions
            ->map(fn (ActivitySubmission $submission) => CarbonImmutable::parse($submission->submitted_at)->startOfDay())
            ->unique(fn (CarbonImmutable $date) => $date->toDateString())
            ->sortBy(fn (CarbonImmutable $date) => $date->getTimestamp())
            ->values();

        if ($dates->count() < $threshold) {
            return null;
        }

        $currentStreak = 0;
        $maxStreak = 0;
        $previousDate = null;

        foreach ($dates as $date) {
            $currentStreak = $previousDate && $date->diffInDays($previousDate) === 1
                ? $currentStreak + 1
                : 1;

            $maxStreak = max($maxStreak, $currentStreak);
            $previousDate = $date;

            if ($currentStreak < $threshold) {
                continue;
            }

            return [
                'awarded_at' => $date,
                'criteria_snapshot' => [
                    'threshold' => $threshold,
                    'max_streak_days' => $maxStreak,
                    'completed_streak_end_date' => $date->toDateString(),
                ],
            ];
        }

        return null;
    }

    /**
     * @param  Collection<int, ActivitySubmission>  $submissions
     * @return array{awarded_at: CarbonImmutable, criteria_snapshot: array<string, mixed>}|null
     */
    private function matchPoints(Collection $submissions, float $threshold): ?array
    {
        if ($threshold <= 0) {
            return null;
        }

        $points = 0.0;

        foreach ($submissions as $submission) {
            $points += round((float) $submission->score, 2);

            if ($points < $threshold) {
                continue;
            }

            return [
                'awarded_at' => CarbonImmutable::parse($submission->submitted_at),
                'criteria_snapshot' => [
                    'threshold' => $threshold,
                    'total_score' => round($points, 2),
                    'activity_submission_id' => $submission->id,
                ],
            ];
        }

        return null;
    }

    /**
     * @return array{awarded_at: CarbonImmutable, criteria_snapshot: array<string, mixed>}|null
     */
    private function matchProfileCompleted(User $student): ?array
    {
        $hasPhoto = filled($student->photo);
        $hasBio = filled($student->bio);
        $hasSocialLink = filled($student->github_url) || filled($student->linkedin_url);

        if (! $hasPhoto || ! $hasBio || ! $hasSocialLink) {
            return null;
        }

        return [
            'awarded_at' => CarbonImmutable::parse($student->updated_at ?? now()),
            'criteria_snapshot' => [
                'has_photo' => $hasPhoto,
                'has_bio' => $hasBio,
                'has_social_link' => $hasSocialLink,
            ],
        ];
    }
}
