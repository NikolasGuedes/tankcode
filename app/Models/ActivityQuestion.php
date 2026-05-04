<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'type',
        'statement',
        'order',
        'correct_option',
        'options',
        'keywords',
        'blank_answers',
        'left_column',
        'right_column',
        'pairs',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'options' => 'array',
            'keywords' => 'array',
            'blank_answers' => 'array',
            'left_column' => 'array',
            'right_column' => 'array',
            'pairs' => 'array',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function submissionAnswers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivitySubmissionAnswer::class);
    }
}
