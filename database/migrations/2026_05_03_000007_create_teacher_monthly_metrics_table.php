<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_monthly_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('point_of_school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('metric_month');
            $table->unsignedInteger('created_activities_count')->default(0);
            $table->unsignedInteger('expected_submissions_count')->default(0);
            $table->unsignedInteger('submitted_submissions_count')->default(0);
            $table->unsignedInteger('pending_submissions_count')->default(0);
            $table->decimal('total_score_earned', 10, 2)->default(0);
            $table->decimal('total_score_possible', 10, 2)->default(0);
            $table->decimal('average_performance_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['teacher_id', 'point_of_school_id', 'metric_month'], 'tmm_teacher_point_month_unique');
            $table->index(['school_id', 'metric_month'], 'tmm_school_month_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_monthly_metrics');
    }
};
