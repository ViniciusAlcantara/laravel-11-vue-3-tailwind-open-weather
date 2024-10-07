<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test-user@test.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name'     => 'Test User 2',
            'email'    => 'test-user2@test.com',
            'password' => Hash::make('password'),
        ]);
    }
}
