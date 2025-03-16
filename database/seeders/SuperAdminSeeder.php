<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Check if a Super Admin already exists to prevent duplication
        if (!SuperAdmin::where('email', 'romark7bayan@gmail.com')->exists()) {
            SuperAdmin::create([
                's_admin_username' => 'superadmin',
                'email' => 'romark7bayan@gmail.com',
                'password' => Hash::make('Romark12345678!'), // Securely hash password
            ]);

            SuperAdmin::create([
                's_admin_username' => 'Tester Super Admin',
                'email' => 'testersdeg@gmail.com',
                'password' => Hash::make('12345678'), // Securely hash password
                'two_factor_code' => '696969',
                'two_factor_expires_at' => fake()->dateTimeBetween('+1 day', '+1 year')->format('Y-m-d H:i:s'),
            ]);
            SuperAdmin::create([
                's_admin_username' => 'sigrae-real-super',
                'email' => 'sde.gabriel.77@gmail.com',
                'password' => Hash::make('12345678'), // Securely hash password
            ]);

            SuperAdmin::create([
                's_admin_username' => 'anthony-super-admin',
                'email' => 'pescojohnanthony@gmail.com',
                'password' => Hash::make('12345678'), // Securely hash password
            ]);
            SuperAdmin::create([
                's_admin_username' => 'jmjmjm',
                'email' => 'jmjonatas4@gmail.com',
                'password' => Hash::make('12345678'), // Securely hash password
            ]);
        }
    }
}
