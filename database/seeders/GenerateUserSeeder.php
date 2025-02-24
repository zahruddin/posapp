<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GenerateUserSeeder extends Seeder
{
    public function run()
    {

        for ($i = 1; $i <= 9; $i++) {
            $users[] = [
                'id_outlet' => 1,
                'name' => "User1 $i",
                'username' => "username1$i",
                'email' => "user1$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ];
        }

        User::insert($users);
    }
}
