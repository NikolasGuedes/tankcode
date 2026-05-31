<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp('awarded_at');
            $table->json('criteria_snapshot')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'achievement_id'], 'student_achievement_unique');
            $table->index(['student_id', 'awarded_at'], 'student_achievement_student_awarded_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_achievements');
    }
};
