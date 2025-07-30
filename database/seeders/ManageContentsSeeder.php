<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManageContents;

class ManageContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ManageContents::create([
            'aboutus1' => 'Reliable Supply Chain: We ensure a consistent and timely delivery of high-quality pharmaceutical products, helping healthcare providers meet their patients\' needs without disruption.',
            'aboutus2' => 'Access to Industry Experts: Our team includes licensed pharmacists, logistics specialists, and compliance officers, ensuring adherence to industry regulations and best practices.',
            'aboutus3' => 'Continuous Innovation: We embrace cutting-edge technology and automation in our inventory and ordering system (RMPOIMS) to enhance efficiency, accuracy, and customer satisfaction.',
            'contact_number' => '09193294773',
            'email' => 'rctmedpharma@gmail.com',
            'address' => 'Riverside, San Juan, Tarlac City, Tarlac, Philippines',
        ]);
    }
}
