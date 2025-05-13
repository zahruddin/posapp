<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KasirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menambahkan data kasir
        User::create([
            'name' => 'Kasir 1',
            'username' => 'kasir123',
            'email' => 'kasir1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',  // Role kasir
        ]);
    }
}
