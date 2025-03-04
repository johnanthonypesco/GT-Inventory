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
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Seed Super Admin
        $this->call(SuperAdminSeeder::class);
        $this->call(LocationSeeder::class);

        //Mga factories ko. Malakas umusok mga ito >:)       
        Product::factory()->count(5)->create();
        Inventory::factory()->count(20)->create();
        
        Company::factory()->count(4)->create();

        $this->call(UserSeeder::class);
        User::factory()->count(10)->create();
        
        Admin::factory()->count(5)->create();
        $this->call(StaffSeeder::class);

        ExclusiveDeal::factory()->count(20)->create();
        Order::factory()->count(12)->create();

        for ($i=0; $i < 10; $i++) { 
            $this->call(OrderSeeder::class);
        }

        Message::factory()->count(6)->create();

    }
}
