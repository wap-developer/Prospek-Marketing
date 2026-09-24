<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo_pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained('todos')->cascadeOnDelete();
            $table->unsignedTinyInteger('task'); // 2..6
            $table->string('file_path', 500);
            $table->string('original_name', 255)->nullable();
            $table->timestamps();

            $table->index(['todo_id', 'task']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_pdfs');
    }
};
