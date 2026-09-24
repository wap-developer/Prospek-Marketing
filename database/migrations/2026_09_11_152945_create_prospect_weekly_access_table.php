<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_weekly_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('month'); // 1..12
            $table->unsignedTinyInteger('week_of_month'); // 1..4
            $table->boolean('is_open')->default(true);
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();

            $table->unique(['prospect_id', 'month', 'week_of_month']);
            $table->index(['prospect_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_weekly_access');
    }
};
