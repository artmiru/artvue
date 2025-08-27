<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $response = $this->post('/login', [
            'phone' => '+7(921)924-52-28',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_authenticate_with_different_phone_formats()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        // Тест с форматом +7
        $response = $this->post('/login', [
            'phone' => '+79219245228',
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        $this->actingAs($user)->post('/logout'); // Выход для следующего теста

        // Тест с форматом 8
        $response = $this->post('/login', [
            'phone' => '89219245228',
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        $this->actingAs($user)->post('/logout'); // Выход для следующего теста

        // Тест с форматом 10 цифр
        $response = $this->post('/login', [
            'phone' => '9219245228',
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
    }

    public function test_users_cannot_authenticate_with_invalid_phone()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $response = $this->post('/login', [
            'phone' => 'invalid-phone',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('phone');
    }

    public function test_users_cannot_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $this->post('/login', [
            'phone' => '+7(921)924-52-28',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_users_are_rate_limited()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'phone' => '+7(921)924-52-28',
                'password' => 'wrong-password',
            ])->assertStatus(302)->assertSessionHasErrors([
                'phone' => 'These credentials do not match our records.',
            ]);
        }

        $response = $this->post('/login', [
            'phone' => '+7(921)924-52-28',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('phone');

        $errors = session('errors');

        $this->assertStringContainsString('Too many login attempts', $errors->first('phone'));
    }
}