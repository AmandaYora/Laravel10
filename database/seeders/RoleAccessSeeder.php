<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoleAccess;

class RoleAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoleAccess::create([
            'role_id' => 1,
            'user_id' => 1,
        ]);

        RoleAccess::create([
            'role_id' => 2,
            'user_id' => 2,
        ]);
    }
}
