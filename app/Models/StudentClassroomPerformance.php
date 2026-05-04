<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassroomPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'point_of_school_id',
        'school_id',
        'total_score',
        'total_possible_score',
        'submitted_activities_count',
        'correct_answers_count',
        'answered_questions_count',
        'accuracy_rate',
        'performance_rate',
        'classroom_rank',
        'point_rank',
        'school_rank',
        'last_submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'total_possible_score' => 'decimal:2',
            'submitted_activities_count' => 'integer',
            'correct_answers_count' => 'integer',
            'answered_questions_count' => 'integer',
            'accuracy_rate' => 'decimal:2',
            'performance_rate' => 'decimal:2',
            'classroom_rank' => 'integer',
            'point_rank' => 'integer',
            'school_rank' => 'integer',
            'last_submitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
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
