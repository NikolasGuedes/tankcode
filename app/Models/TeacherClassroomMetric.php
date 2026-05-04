<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherClassroomMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'point_of_school_id',
        'school_id',
        'top_student_id',
        'classroom_status',
        'students_count',
        'total_activities_count',
        'published_activities_count',
        'draft_activities_count',
        'total_questions_count',
        'available_points',
        'expected_submissions_count',
        'submitted_submissions_count',
        'pending_submissions_count',
        'total_score_earned',
        'total_score_possible',
        'average_completion_rate',
        'average_performance_rate',
        'top_student_score',
        'top_student_accuracy_rate',
    ];

    protected function casts(): array
    {
        return [
            'students_count' => 'integer',
            'total_activities_count' => 'integer',
            'published_activities_count' => 'integer',
            'draft_activities_count' => 'integer',
            'total_questions_count' => 'integer',
            'available_points' => 'decimal:2',
            'expected_submissions_count' => 'integer',
            'submitted_submissions_count' => 'integer',
            'pending_submissions_count' => 'integer',
            'total_score_earned' => 'decimal:2',
            'total_score_possible' => 'decimal:2',
            'average_completion_rate' => 'decimal:2',
            'average_performance_rate' => 'decimal:2',
            'top_student_score' => 'decimal:2',
            'top_student_accuracy_rate' => 'decimal:2',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function topStudent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'top_student_id');
    }
}
