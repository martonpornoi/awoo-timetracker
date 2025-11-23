<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        // Update if exists; create if not
        User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('test'),
                'is_admin' => true,
            ]
        );
    }
}