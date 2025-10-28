<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase; // Resets DB after each test
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase; // Use this trait for database interactions

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login'); // Make a GET request
        $response->assertStatus(200);      // Check if the page loaded okay
        $response->assertSee('Email');    // Check if the word "Email" is on the page
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        // Create a user first (using a factory is common)
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to login
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard'); // Check if redirected to dashboard
        $this->assertAuthenticatedAs($user);     // Check if the correct user is logged in
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email'); // Check for login error
        $this->assertGuest();                      // Check that the user is NOT logged in
    }
}