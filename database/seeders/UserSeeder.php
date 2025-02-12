<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Membuat user dengan role admin
        User::create([
            'name' => 'Admin User',
            'username' => 'admin123',  // username untuk login
            'email' => 'admin@example.com',  // email untuk login
            'password' => Hash::make('password123'),  // password untuk login
            'role' => 'admin',  // role sebagai admin
        ]);
    }
}
