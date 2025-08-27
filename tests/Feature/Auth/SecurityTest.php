<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_brute_force_protection_on_login()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        // Попытки входа с неверным паролем
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/login', [
                'phone' => '+7(921)924-52-28',
                'password' => 'wrong-password',
            ]);
            
            // После 5 попыток должна быть ошибка о превышении лимита
            if ($i >= 5) {
                $response->assertSessionHasErrors('phone');
                $this->assertStringContainsString('Too many login attempts', session('errors')->first('phone'));
            }
        }
    }

    public function test_brute_force_protection_on_password_reset()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        // Попытки сброса пароля
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/forgot-password', [
                'phone' => '+7(921)924-52-28',
            ]);
            
            // После 3 попыток должна быть ошибка о превышении лимита
            if ($i >= 3) {
                $response->assertSessionHasErrors('phone');
                $this->assertStringContainsString('Too many attempts', session('errors')->first('phone'));
            }
        }
    }

    public function test_xss_protection_on_phone_input()
    {
        $response = $this->post('/login', [
            'phone' => '<script>alert("xss")</script>',
            'password' => 'password',
        ]);

        // Должна быть ошибка валидации, а не выполнение скрипта
        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_sql_injection_protection_on_phone_input()
    {
        $response = $this->post('/login', [
            'phone' => "'; DROP TABLE users; --",
            'password' => 'password',
        ]);

        // Должна быть ошибка валидации, а не выполнение SQL
        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }
}