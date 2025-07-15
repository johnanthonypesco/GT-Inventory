<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Check if a Super Admin already exists to prevent duplication
        if (!Admin::where('email', 'romark7bayan@gmail.com')->exists()) {
            Admin::create([
                'username' => 'Admin',
                'email' => 'jg.jonatas.au@phinmaed.com',
                'password' => Hash::make('12345678'), // Securely hash password
                'super_admin_id' => 2,
                'is_admin' => 1,
                'archived_at' => null, // Uncomment and set value if needed
                'contact_number' => '0127138387', // Uncomment and set value if needed
            ]);

           
        }
    }
}
