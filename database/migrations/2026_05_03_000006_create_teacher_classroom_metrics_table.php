<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_classroom_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('point_of_school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('top_student_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('classroom_status')->default('active');
            $table->unsignedInteger('students_count')->default(0);
            $table->unsignedInteger('total_activities_count')->default(0);
            $table->unsignedInteger('published_activities_count')->default(0);
            $table->unsignedInteger('draft_activities_count')->default(0);
            $table->unsignedInteger('total_questions_count')->default(0);
            $table->decimal('available_points', 10, 2)->default(0);
            $table->unsignedInteger('expected_submissions_count')->default(0);
            $table->unsignedInteger('submitted_submissions_count')->default(0);
            $table->unsignedInteger('pending_submissions_count')->default(0);
            $table->decimal('total_score_earned', 10, 2)->default(0);
            $table->decimal('total_score_possible', 10, 2)->default(0);
            $table->decimal('average_completion_rate', 5, 2)->default(0);
            $table->decimal('average_performance_rate', 5, 2)->default(0);
            $table->decimal('top_student_score', 10, 2)->default(0);
            $table->decimal('top_student_accuracy_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['teacher_id', 'classroom_id'], 'tcm_teacher_classroom_unique');
            $table->index(['school_id', 'teacher_id'], 'tcm_school_teacher_index');
            $table->index(['point_of_school_id', 'teacher_id'], 'tcm_point_teacher_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_classroom_metrics');
    }
};
