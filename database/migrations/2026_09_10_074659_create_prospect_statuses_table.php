<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 32)->unique();
            $table->string('name', 64);
            $table->timestamps();
        });

        DB::table('prospect_statuses')->insert([
            ['slug' => 'open',    'name' => 'Open',    'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'closing', 'name' => 'Closing', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'cancel',  'name' => 'Cancel',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_statuses');
    }
};
