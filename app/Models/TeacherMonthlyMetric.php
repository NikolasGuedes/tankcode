<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherMonthlyMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'point_of_school_id',
        'school_id',
        'metric_month',
        'created_activities_count',
        'expected_submissions_count',
        'submitted_submissions_count',
        'pending_submissions_count',
        'total_score_earned',
        'total_score_possible',
        'average_performance_rate',
    ];

    protected function casts(): array
    {
        return [
            'metric_month' => 'date',
            'created_activities_count' => 'integer',
            'expected_submissions_count' => 'integer',
            'submitted_submissions_count' => 'integer',
            'pending_submissions_count' => 'integer',
            'total_score_earned' => 'decimal:2',
            'total_score_possible' => 'decimal:2',
            'average_performance_rate' => 'decimal:2',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function pointOfSchool(): BelongsTo
    {
        return $this->belongsTo(PointOfSchool::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
