<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('level');
            $table->unsignedInteger('questions_count');
            $table->decimal('points_per_question', 5, 2);
            $table->decimal('total_points', 8, 2);
            $table->date('due_date')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['teacher_id', 'status']);
            $table->index(['classroom_id', 'status']);
            $table->index(['teacher_id', 'classroom_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
