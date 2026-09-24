<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('client_phone', 32);
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->date('entry_date');
            $table->time('entry_time');
            $table->foreignId('sender_id')->constrained('senders')->restrictOnDelete();
            $table->foreignId('group_id')->constrained('groups')->restrictOnDelete();
            $table->foreignId('source_id')->constrained('prospect_sources')->restrictOnDelete();
            $table->foreignId('marketing_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('status_id')->constrained('prospect_statuses')->restrictOnDelete();
            $table->decimal('nominal_closing', 15, 2)->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('marketing_user_id');
            $table->index('status_id');
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
