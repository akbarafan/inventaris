<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@inventaris.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'petugas',
            'email' => 'petugas@inventaris.com',
            'password' => bcrypt('petugas123'),
            'role' => 'petugas',
        ]);
    }
}
