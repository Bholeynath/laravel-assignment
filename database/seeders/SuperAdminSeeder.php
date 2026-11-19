<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'superadmin@gmail.com';

        // Check if SuperAdmin already exists
        $existingUser = DB::selectOne(
            'SELECT id FROM users WHERE email = ?',
            [$email]
        );

        if ($existingUser) {
            return;
        }

        // Create SuperAdmin
        DB::insert(
            'INSERT INTO users
            (name, email, email_verified_at, password, company_id, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'Super Admin',
                $email,
                now(),
                Hash::make('SuperAdmin@123'),
                null,
                'SuperAdmin',
                now(),
                now()
            ]
        );
    }
}
