<?php

namespace App\Models;

use App\Enums\RoleEnum;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'school_id',
        'name',
        'email',
        'password',
        'photo',
        'bio',
        'github_url',
        'linkedin_url',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function pointOfSchools(): BelongsToMany
    {
        return $this->belongsToMany(PointOfSchool::class)
            ->withPivot(['title', 'is_primary', 'status'])
            ->withTimestamps();
    }

    public function classroomsAsTeacher(): HasMany
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function activitiesAsTeacher(): HasMany
    {
        return $this->hasMany(Activity::class, 'teacher_id');
    }

    public function activitySubmissions(): HasMany
    {
        return $this->hasMany(ActivitySubmission::class, 'student_id');
    }

    public function activitySubmissionAnswers(): HasManyThrough
    {
        return $this->hasManyThrough(
            ActivitySubmissionAnswer::class,
            ActivitySubmission::class,
            'student_id',
            'activity_submission_id',
            'id',
            'id',
        );
    }

    public function studentClassroomPerformances(): HasMany
    {
        return $this->hasMany(StudentClassroomPerformance::class, 'student_id');
    }

    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class, 'student_id');
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'student_achievements', 'student_id', 'achievement_id')
            ->withPivot(['awarded_at', 'criteria_snapshot'])
            ->withTimestamps();
    }

    public function teacherClassroomMetrics(): HasMany
    {
        return $this->hasMany(TeacherClassroomMetric::class, 'teacher_id');
    }

    public function topStudentMetrics(): HasMany
    {
        return $this->hasMany(TeacherClassroomMetric::class, 'top_student_id');
    }

    public function teacherMonthlyMetrics(): HasMany
    {
        return $this->hasMany(TeacherMonthlyMetric::class, 'teacher_id');
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student')->withTimestamps();
    }

    public function hasRole(RoleEnum|string ...$roles): bool
    {
        $roleName = $this->role?->name;

        if (! $roleName instanceof RoleEnum) {
            return false;
        }

        return collect($roles)
            ->map(fn (RoleEnum|string $role) => $role instanceof RoleEnum ? $role : RoleEnum::tryFrom($role))
            ->filter()
            ->contains($roleName);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function homeRouteName(): string
    {
        return $this->role?->name?->dashboardRoute() ?? 'dashboard';
    }

    public function supportsPointOfSchoolContext(): bool
    {
        return $this->hasRole(RoleEnum::OWNER, RoleEnum::DIRECTOR, RoleEnum::TEACHER);
    }
}
