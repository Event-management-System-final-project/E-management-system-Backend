<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = [
            [
                'firstName' => 'Yohannes',
                'lastName' => 'Admin',
                'role' => 'admin',
                'email' => 'yohannes@gmail.com',
                'image' => '',
                'email_verified_at' => now(),
                'password' => Hash::make('yohannes1234'),
            ],
            [
                'firstName' => 'Yoha',
                'lastName' => 'Organizer',
                'role' => 'organizer',
                'email' => 'yoha@gmail.com',
                'image' => '',
                'email_verified_at' => now(),
                'password' => Hash::make('yoha1234'),
            ],
            [
                'firstName' => 'Ela',
                'lastName' => 'User',
                'role' => 'user',
                'email' => 'ela@gmail.com',
                'image' => '',
                'email_verified_at' => now(),
                'password' => Hash::make('ela1234'),
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
             unset($userData['role']);

            $user = User::create($userData);
            $user->assignRole($roleName); // Spatie assigns existing role
    }

}

}
