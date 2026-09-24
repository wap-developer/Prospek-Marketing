<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospect_weekly_updates', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->default(2026)->after('user_id');
            $table->unsignedTinyInteger('iso_week')->default(1)->after('year');

            $table->dropUnique('prospect_weekly_updates_prospect_id_month_week_of_month_unique');
            $table->unique(['prospect_id', 'year', 'iso_week'], 'pwu_prospect_year_iso_week_unique');
            $table->index(['year', 'iso_week'], 'pwu_year_iso_week_index');
        });
    }

    public function down(): void
    {
        Schema::table('prospect_weekly_updates', function (Blueprint $table) {
            $table->dropUnique('pwu_prospect_year_iso_week_unique');
            $table->dropIndex('pwu_year_iso_week_index');
            $table->unique(['prospect_id', 'month', 'week_of_month'], 'prospect_weekly_updates_prospect_id_month_week_of_month_unique');
            $table->dropColumn(['year', 'iso_week']);
        });
    }
};
