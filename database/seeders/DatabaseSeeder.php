<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $accounts = [
            ['super_admin',       'Super Admin',       'super@prospek.local'],
            ['cs',                'Customer Service',  'cs@prospek.local'],
            ['marketing',         'Marketing',         'marketing@prospek.local'],
            ['manager_marketing', 'Manager Marketing', 'manager@prospek.local'],
        ];

        foreach ($accounts as [$slug, $name, $email]) {
            $roleId = Role::where('slug', $slug)->value('id');

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'username' => $slug,
                    'password' => Hash::make('password'),
                    'role_id' => $roleId,
                ],
            );
        }
    }
}
