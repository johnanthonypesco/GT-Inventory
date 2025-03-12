<?php

use App\Models\User;


describe('Account Logins', function () {
    test('Customer can access their login page.', function () {
        $request = $this->get('/login');
    
        $request->assertStatus(200);
    });
    test('Staff can access their login page.', function () {
        $request = $this->get('/staff/login');
    
        $request->assertStatus(200);
    });
    test('Admin can access their login page.', function () {
        $request = $this->get('/admin/login');
    
        $request->assertStatus(200);
    });
    test('Superadmin can access their login page.', function () {
        $request = $this->get('/superadmin/login');
    
        $request->assertStatus(200);
    });
})->group('account-login');

describe("Account Login Attempts", function () {
    test("Customer can login", function () {
        // dd(
        //     User::all()->toArray()
        // );
        
        $response = $this->post('/login', [
            'email' => 'sies.gabriel.au@phinmaed.com', 
            'password' => Hash::make('12345678'),
        ]);

        $response->assertRedirect('/verify-email');

        // $this->assertAuthenticatedAs($account);
    });
})->group("account-login-attempts");