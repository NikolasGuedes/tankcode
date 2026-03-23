<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_of_school_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('point_of_school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['point_of_school_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_of_school_user');
    }
};
