<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Default existing rows to "marketing" so FK + NOT NULL stays safe.
        $marketingId = DB::table('roles')->where('slug', 'marketing')->value('id');

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('password')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        if ($marketingId) {
            DB::table('users')->whereNull('role_id')->update(['role_id' => $marketingId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
