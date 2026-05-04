<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointOfSchool extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'cnpj',
        'address_line',
        'zip_code',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['title', 'is_primary', 'status'])
            ->withTimestamps();
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function studentClassroomPerformances(): HasMany
    {
        return $this->hasMany(StudentClassroomPerformance::class);
    }

    public function teacherClassroomMetrics(): HasMany
    {
        return $this->hasMany(TeacherClassroomMetric::class);
    }

    public function teacherMonthlyMetrics(): HasMany
    {
        return $this->hasMany(TeacherMonthlyMetric::class);
    }
}
