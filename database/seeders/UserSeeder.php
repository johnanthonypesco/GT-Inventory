<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 69,
            'location_id' => 2,
            'company_id' => 1,
            'name' => 'yahoo baby!',
            'email' => 'sies.gabriel.au@phinmaed.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'contact_number' => '09287337991',
        ]);
        User::create([
            'id' => 2000,
            'location_id' => 2,
            'company_id' => 1,
            'name' => 'yahoo baby!',
            'email' => 'pescojohnanthony@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'contact_number' => '09287337991',
        ]);
    }
}
