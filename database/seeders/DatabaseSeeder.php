<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Pemda Jabar',
                'username' => 'pemda_jabar',
                'password' => Hash::make('pemda_jabar'),
                'is_admin' => false,
                'is_verified' => true,
            ],
            [
                'name' => 'Pemda Jateng',
                'username' => 'pemda_jateng',
                'password' => Hash::make('pemda_jateng'),
                'is_admin' => false,
                'is_verified' => true,
            ],
            [
                'name' => 'Pemda Jatim',
                'username' => 'pemda_jatim',
                'password' => Hash::make('pemda_jatim'),
                'is_admin' => false,
                'is_verified' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}
