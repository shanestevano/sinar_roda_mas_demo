<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SINAR RODA MAS');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'kasir@sinarrodamas.com',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'kasir@sinarrodamas.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'blocked@sinarrodamas.com',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'blocked@sinarrodamas.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_quick_login_works_for_demo(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->get('/quick-login/admin');

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard'));
    }
}
