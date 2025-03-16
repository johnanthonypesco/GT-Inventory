<?php

use App\Models\Admin;
use App\Models\Company;
use App\Models\Location;
use App\Models\Staff;
use App\Models\SuperAdmin;
use App\Models\User;

describe("Customer Related Factory Tests", function () {
    it("can create 2 locations", function (){
        $locations = Location::factory()->count(2)->create();

        expect($locations)->not->toBeNull();
        expect(count($locations))->toBe(2);
    } );
    
    it('can create 3 companies', function () {
        $companies = Company::factory()->count(3)->create([
            'name' => 'test company',
            'location_id' => 1,
        ]);
        
        expect($companies)->not->toBeNull();
        expect(count($companies->where('name', 'test company')))->toBe(3);
    }); 

    it("can create 10 Users:", function () {        
        $users = User::factory()->count(10)->create(['name' => 'tests']);

        expect($users)->not->toBeNull();
        expect(count($users->where('name', 'tests')->all()))->toBe(10);
    });
});


describe('Employee Related Factories', function () {
    it('can seed all Super Admins', function () {
        $seeded = SuperAdmin::count();

        expect($seeded)->not->toBeNull();
        expect($seeded)->toBe(5);
    });
    
    it("can create 5 Admins", function () {
        $admins = Admin::factory()->count(5)->create();

        expect($admins)->not->toBeNull();
        expect(count($admins))->toBe(5);
    });

    it('can create a pair of staffs', function () {
        $staffs = Staff::factory()->count(2)->create();

        expect($staffs)->not->toBeNull();
        expect(count($staffs))->toBe(2);
    }); 
});