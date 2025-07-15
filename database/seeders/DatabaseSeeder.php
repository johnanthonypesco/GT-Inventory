<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\ExclusiveDeal;
use App\Models\Inventory;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Seed Super Admin and Locations first
        $this->call(SuperAdminSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(LocationSeeder::class);

        // Create base products and inventory
        Product::factory()->count(24)->create();
        Inventory::factory()->count(20)->create();
        
        // Create companies and users
        Company::factory()->count(4)->create();
        $this->call(UserSeeder::class);
        User::factory()->count(10)->create();
        
        // Create admins and staff
        Admin::factory()->count(5)->create();
        $this->call(StaffSeeder::class);

        // Create exclusive deals
        ExclusiveDeal::factory()->count(20)->create();

        // Seed seasonal data - moved before additional orders
        $this->call(SeasonalDataSeeder::class);

        // Create additional orders
        Order::factory()->count(12)->create();
        for ($i=0; $i < 10; $i++) { 
            $this->call(OrderSeeder::class);
        }

        // Create messages
        Message::factory()->count(6)->create();
    }
}