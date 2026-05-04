<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_submission_answers')) {
            Schema::create('activity_submission_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('activity_submission_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('activity_question_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->json('answer_payload');
                $table->boolean('is_correct')->default(false);
                $table->decimal('earned_points', 8, 2)->default(0);
                $table->timestamps();
            });
        }

        Schema::table('activity_submission_answers', function (Blueprint $table) {
            if (! Schema::hasIndex('activity_submission_answers', 'asa_submission_question_unique')) {
                $table->unique(['activity_submission_id', 'activity_question_id'], 'asa_submission_question_unique');
            }

            if (! Schema::hasIndex('activity_submission_answers', 'asa_question_correct_index')) {
                $table->index(['activity_question_id', 'is_correct'], 'asa_question_correct_index');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_submission_answers');
    }
};
