<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 32)->unique();
            $table->string('name', 64);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['slug' => 'super_admin',         'name' => 'Super Admin',       'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'cs',                  'name' => 'Customer Service',  'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'marketing',           'name' => 'Marketing',         'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'manager_marketing',   'name' => 'Manager Marketing', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
