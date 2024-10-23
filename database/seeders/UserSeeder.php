<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'superadmin',
            'code' => 'superadmin',
            'phone' => '1234567890',
            'email' => 'superadmin@yopmail.com',
            'password' => 'superadmin',
            'token' => null,
            'nonce' => null,
            'is_verify' => 1,
        ]);

        User::create([
            'name' => 'admin',
            'code' => 'admin',
            'phone' => '0987654321',
            'email' => 'admin@yopmail.com',
            'password' => 'admin',
            'token' => null,
            'nonce' => null,
            'is_verify' => 1,
        ]);
    }
}
