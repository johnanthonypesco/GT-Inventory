<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Location;
use App\Models\Staff;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Staff::where('email', 'testersdeg@gmail.com')->exists()) {
            Staff::create([
                'admin_id' => Admin::pluck('id')->random(),
                'location_id' => Location::pluck('id')->random(),
                'staff_username' => 'SDEG77 (Staff)',
                'email' => 'testersdeg@gmail.com',
                'password' => Hash::make('12345678'),
                'job_title' => 'Janitor',
                'is_staff' => true,
            ]);
            Staff::create([
                'admin_id' => Admin::pluck('id')->random(),
                'location_id' => Location::pluck('id')->random(),
                'staff_username' => 'SDEG78 (Staff)',
                'email' => 'pescojohnanthony@gmail.com',
                'password' => Hash::make('12345678'),
                'job_title' => 'Employee',
                'is_staff' => true,
            ]);
        }
    }
}
