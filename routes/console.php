<?php

use App\Support\PerformanceMetricsRebuilder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('metrics:recalculate {--school_id=} {--teacher_id=} {--classroom_id=}', function (PerformanceMetricsRebuilder $rebuilder) {
    $schoolId = $this->option('school_id');
    $teacherId = $this->option('teacher_id');
    $classroomId = $this->option('classroom_id');

    if ($classroomId) {
        $rebuilder->rebuildClassroom((int) $classroomId);
        $this->info("Métricas recalculadas para a turma {$classroomId}.");

        return;
    }

    if ($teacherId) {
        $rebuilder->rebuildTeacher((int) $teacherId);
        $this->info("Métricas recalculadas para o professor {$teacherId}.");

        return;
    }

    if ($schoolId) {
        $rebuilder->rebuildSchool((int) $schoolId);
        $this->info("Métricas recalculadas para a escola {$schoolId}.");

        return;
    }

    $rebuilder->rebuildAll();
    $this->info('Métricas recalculadas para todas as escolas.');
})->purpose('Recalcula scores, rankings e métricas agregadas.');
