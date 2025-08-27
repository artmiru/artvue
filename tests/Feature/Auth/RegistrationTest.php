<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'phone' => '+7(921)924-52-28',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_users_cannot_register_with_invalid_phone()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'phone' => 'invalid-phone',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('phone');
    }

    public function test_new_users_cannot_register_with_duplicate_phone()
    {
        // Создание пользователя с номером телефона
        $user = \App\Models\User::factory()->create([
            'phone' => '9219245228',
        ]);

        // Попытка регистрации с тем же номером телефона
        $response = $this->post('/register', [
            'name' => 'Test User',
            'phone' => '+7(921)924-52-28',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('phone');
    }
}