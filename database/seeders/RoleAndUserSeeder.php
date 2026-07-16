<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Spatie\Permission\Models\Role::create(['name' => 'Super Admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'HR Manager']);

        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $superAdmin->assignRole('Super Admin');

        $hrManager = \App\Models\User::create([
            'name' => 'HR Manager',
            'email' => 'hr@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $hrManager->assignRole('HR Manager');
    }
}
