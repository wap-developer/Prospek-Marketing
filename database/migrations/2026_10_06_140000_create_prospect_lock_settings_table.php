<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prospect_lock_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_locked')->default(false);
            $table->string('last_scheduled_slot', 50)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_lock_settings');
    }
};
