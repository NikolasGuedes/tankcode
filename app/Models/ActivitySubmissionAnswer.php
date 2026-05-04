<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySubmissionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_submission_id',
        'activity_question_id',
        'answer_payload',
        'is_correct',
        'earned_points',
    ];

    protected function casts(): array
    {
        return [
            'answer_payload' => 'array',
            'is_correct' => 'boolean',
            'earned_points' => 'decimal:2',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ActivitySubmission::class, 'activity_submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ActivityQuestion::class, 'activity_question_id');
    }
}
