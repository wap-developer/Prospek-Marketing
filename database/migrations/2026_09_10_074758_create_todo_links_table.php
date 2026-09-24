<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained('todos')->cascadeOnDelete();
            $table->enum('platform', ['instagram', 'tiktok', 'facebook', 'snack_video']);
            $table->unsignedTinyInteger('slot'); // 1..3
            $table->string('url', 500);
            $table->timestamps();

            $table->unique(['todo_id', 'platform', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_links');
    }
};
