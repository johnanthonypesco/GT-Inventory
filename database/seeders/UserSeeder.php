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
            'company_id' => 1,
            'name' => 'yahoo baby!',
            'email' => 'sies.gabriel.au@phinmaed.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'contact_number' => '09287337991',
        ]);
        User::create([
            'id' => 70,
            'company_id' => 2,
            'name' => 'John Pesgo',
            'email' => 'pescojohnanthony@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'contact_number' => '09287337992',
        ]);
        // bawasan customer user for testing :)

        // User::create([
        //     'id' => 70,
        //     'company_id' => 1,
        //     'name' => 'Test Name 1',
        //     'email' => 'test@gmail.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('12345678'),
        //     'contact_number' => '09287337991',
        // ]);
        // User::create([
        //     'id' => 71,
        //     'company_id' => 1,
        //     'name' => 'Test Name 2',
        //     'email' => 'test1@gmail.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('12345678'),
        //     'contact_number' => '09287337991',
        // ]);
        // User::create([
        //     'id' => 72,
        //     'company_id' => 1,
        //     'name' => 'Test Name 3',
        //     'email' => 'test2@gmail.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('12345678'),
        //     'contact_number' => '09287337991',
        // ]);
    }

}
