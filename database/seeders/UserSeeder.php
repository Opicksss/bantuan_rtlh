<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Verifikator',
            'email' => 'veri@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
        ]);

        \App\Models\User::create([
            'name' => 'Dhani',
            'email' => 'romadani.code@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
        ]);


    }
}
