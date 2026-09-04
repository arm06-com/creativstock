<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Mridha Nest',
                'email' => 'mridha.nest@gmail.com',
                'password' => Hash::make('Passord01'),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
                'active' => true,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('test12341'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'active' => true,
            ],
            [
                'name' => 'Contributor User',
                'email' => 'contributor@example.com',
                'password' => Hash::make('test12341'),
                'role' => User::ROLE_CONTRIBUTOR,
                'email_verified_at' => now(),
                'active' => true,
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'password' => Hash::make('test12341'),
                'role' => User::ROLE_CUSTOMER,
                'email_verified_at' => now(),
                'active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
