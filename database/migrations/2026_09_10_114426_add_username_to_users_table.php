<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
        });

        DB::table('users')->whereNull('username')->orderBy('id')->each(function ($u) {
            $base = Str::slug(Str::before($u->email, '@'), '_');
            $candidate = $base ?: 'user'.$u->id;
            $i = 1;
            while (DB::table('users')->where('username', $candidate)->where('id', '!=', $u->id)->exists()) {
                $candidate = $base.$i++;
            }
            DB::table('users')->where('id', $u->id)->update(['username' => $candidate]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
