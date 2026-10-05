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
        Schema::table('todo_exports', function (Blueprint $table) {
            $table->string('export_type', 30)->default('monthly')->after('year');
            $table->date('start_date')->nullable()->after('export_type');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('period_label')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todo_exports', function (Blueprint $table) {
            $table->dropColumn(['export_type', 'start_date', 'end_date', 'period_label']);
        });
    }
};
