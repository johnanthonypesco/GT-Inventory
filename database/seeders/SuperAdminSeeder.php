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
        }
    }
}
