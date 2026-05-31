<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'image_path',
        'rule_type',
        'rule_config',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rule_config' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_achievements', 'achievement_id', 'student_id')
            ->withPivot(['awarded_at', 'criteria_snapshot'])
            ->withTimestamps();
    }
}
