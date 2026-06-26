<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->insertOrIgnore([
            'username'   => env('ADMIN_USERNAME', 'admin'),
            'password'   => Hash::make(env('ADMIN_PASSWORD', 'change_me_now')),
            'name'       => env('ADMIN_NAME', 'Administrator'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
