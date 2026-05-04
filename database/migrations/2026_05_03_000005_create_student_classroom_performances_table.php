<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_classroom_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('point_of_school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('total_score', 10, 2)->default(0);
            $table->decimal('total_possible_score', 10, 2)->default(0);
            $table->unsignedInteger('submitted_activities_count')->default(0);
            $table->unsignedInteger('correct_answers_count')->default(0);
            $table->unsignedInteger('answered_questions_count')->default(0);
            $table->decimal('accuracy_rate', 5, 2)->default(0);
            $table->decimal('performance_rate', 5, 2)->default(0);
            $table->unsignedInteger('classroom_rank')->default(0);
            $table->unsignedInteger('point_rank')->default(0);
            $table->unsignedInteger('school_rank')->default(0);
            $table->timestamp('last_submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'classroom_id'], 'scp_student_classroom_unique');
            $table->index(['school_id', 'school_rank'], 'scp_school_rank_index');
            $table->index(['point_of_school_id', 'point_rank'], 'scp_point_rank_index');
            $table->index(['classroom_id', 'classroom_rank'], 'scp_classroom_rank_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_classroom_performances');
    }
};
