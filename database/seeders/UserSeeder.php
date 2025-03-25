<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Kasir
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@pos.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
