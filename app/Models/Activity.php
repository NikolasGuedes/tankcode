<?php

namespace App\Models;

use App\Enums\ActivityLevelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'teacher_id',
        'title',
        'description',
        'level',
        'questions_count',
        'points_per_question',
        'total_points',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'level' => ActivityLevelEnum::class,
            'questions_count' => 'integer',
            'points_per_question' => 'decimal:2',
            'total_points' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ActivityQuestion::class)->orderBy('order');
    }
}
