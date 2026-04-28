<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@unipaz.edu.co'],
            [
                'name'              => 'Administrador UNIPAZ',
                'password'          => Hash::make('Admin2024*'),
                'role'              => 'admin',
                'email_verified_at' => now(),
                'active'            => true,
            ]
        );
    }
}
