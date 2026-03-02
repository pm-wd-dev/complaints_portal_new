<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@supportportal.com',
            'password' => Hash::make('Admin@2024Secure'),
            'role' => 'admin',
        ]);
    
        // Respondent user
        User::create([
            'name' => 'Support Team Member',
            'email' => 'respondent@supportportal.com',
            'password' => Hash::make('Respond#2024Safe'),
            'role' => 'respondent',
        ]);
    }
}
