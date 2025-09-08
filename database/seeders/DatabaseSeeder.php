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
use App\Models\ManageContents;
use App\Models\ImmutableHistory;
use App\Models\PurchaseOrder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Seed Super Admin and Locations first
        $this->call(SuperAdminSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(ManageContentsSeeder::class);

        // Create base products and inventory
        Product::factory()
        ->configure()
        ->perPair(2)
        ->count(23 * 2)
        ->create();

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

        PurchaseOrder::factory()->count(40)->create();

        // Create additional orders
        Order::factory()->count(120)->create(); // number of random orders
        for ($i=0; $i < 30; $i++) { // number of yahoo baby orders
            $this->call(OrderSeeder::class); 
        }

        // Create order records to order history page
        ImmutableHistory::factory()->count(50)->create(); // number of random orders

        // Create messages
        Message::factory()->count(6)->create();
    }
}