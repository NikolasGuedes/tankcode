<?php

namespace App\Support;

use App\Enums\ActivityLevelEnum;

class ActivityPointsCalculator
{
    /**
     * @return array{points_per_question: float, total_points: float}
     */
    public function calculate(string|ActivityLevelEnum $level, int $questionsCount): array
    {
        $level = $level instanceof ActivityLevelEnum ? $level : ActivityLevelEnum::from($level);
        $pointsPerQuestion = $level->pointsPerQuestion();

        return [
            'points_per_question' => $pointsPerQuestion,
            'total_points' => $questionsCount * $pointsPerQuestion,
        ];
    }
}
