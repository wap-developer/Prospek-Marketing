<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo_weekly_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1..12
            $table->unsignedTinyInteger('week');  // 1..4
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month', 'week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_weekly_reflections');
    }
};
