<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_weekly_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('week'); // 1..53
            $table->text('note');
            $table->string('progress', 16)->default('on_track'); // on_track | slow | stuck | closed
            $table->timestamps();

            $table->index(['prospect_id', 'year', 'week']);
            $table->unique(['prospect_id', 'year', 'week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_weekly_updates');
    }
};
