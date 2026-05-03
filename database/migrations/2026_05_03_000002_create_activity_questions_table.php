<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('type');
            $table->text('statement');
            $table->unsignedInteger('order')->default(0);
            $table->string('correct_option')->nullable();
            $table->json('options')->nullable();
            $table->json('keywords')->nullable();
            $table->json('blank_answers')->nullable();
            $table->json('left_column')->nullable();
            $table->json('right_column')->nullable();
            $table->json('pairs')->nullable();
            $table->timestamps();

            $table->index(['activity_id', 'order']);
            $table->index(['activity_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_questions');
    }
};
