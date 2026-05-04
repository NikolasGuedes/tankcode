<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status')->default('submitted');
            $table->timestamp('submitted_at');
            $table->decimal('score', 8, 2);
            $table->decimal('total_points', 8, 2);
            $table->unsignedInteger('correct_answers_count')->default(0);
            $table->timestamps();

            $table->unique(['activity_id', 'student_id']);
            $table->index(['student_id', 'submitted_at']);
            $table->index(['classroom_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_submissions');
    }
};
