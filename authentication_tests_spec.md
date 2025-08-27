# Спецификация обновления тестов аутентификации

## Общие требования

1. Обновить существующие тесты для работы с номером телефона
2. Добавить новые тесты для проверки валидации телефона
3. Добавить тесты для форматирования номера телефона
4. Обновить тесты сброса пароля
5. Добавить тесты для проверки безопасности

## Тесты аутентификации

### Файл: tests/Feature/Auth/AuthenticationTest.php

### Обновленные тесты:
```php
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
```

## Тесты регистрации

### Файл: tests/Feature/Auth/RegistrationTest.php

### Обновленные тесты:
```php
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
```

## Тесты сброса пароля

### Файл: tests/Feature/Auth/PasswordResetTest.php

### Обновленные тесты:
```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested()
    {
        Notification::fake();

        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $this->post('/forgot-password', ['phone' => '+7(921)924-52-28']);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        Notification::fake();

        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $this->post('/forgot-password', ['phone' => '+7(921)924-52-28']);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        Notification::fake();

        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $this->post('/forgot-password', ['phone' => '+7(921)924-52-28']);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'phone' => '+7(921)924-52-28',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }

    public function test_password_cannot_be_reset_with_invalid_phone()
    {
        Notification::fake();

        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        $this->post('/forgot-password', ['phone' => '+7(921)924-52-28']);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'phone' => 'invalid-phone',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasErrors('phone');

            return true;
        });
    }
}
```

## Тесты валидации номера телефона

### Файл: tests/Unit/PhoneValidationTest.php

### Новые тесты:
```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\PhoneHelper;

class PhoneValidationTest extends TestCase
{
    public function test_extract_digits_from_phone()
    {
        $this->assertEquals('9219245228', extractDigits('+79219245228'));
        $this->assertEquals('9219245228', extractDigits('89219245228'));
        $this->assertEquals('9219245228', extractDigits('9219245228'));
        $this->assertEquals('9219245228', extractDigits('+7(921)924-52-28'));
        $this->assertEquals('9219245228', extractDigits('8(921)924-52-28'));
        $this->assertNull(extractDigits('invalid'));
        $this->assertNull(extractDigits('123'));
        $this->assertNull(extractDigits(''));
    }

    public function test_validate_phone()
    {
        $this->assertTrue(validatePhone('+79219245228'));
        $this->assertTrue(validatePhone('89219245228'));
        $this->assertTrue(validatePhone('9219245228'));
        $this->assertTrue(validatePhone('+7(921)924-52-28'));
        
        // Некорректные номера
        $this->assertFalse(validatePhone('invalid'));
        $this->assertFalse(validatePhone('1234567890')); // Некорректный код оператора
        $this->assertFalse(validatePhone(''));
        $this->assertFalse(validatePhone('921924522')); // Мало цифр
        $this->assertFalse(validatePhone('92192452289')); // Много цифр
    }

    public function test_format_phone()
    {
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('+79219245228'));
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('89219245228'));
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('9219245228'));
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('+7(921)924-52-28'));
        
        // Некорректные номера
        $this->assertEquals('invalid', formatPhone('invalid'));
        $this->assertEquals('', formatPhone(''));
    }

    public function test_is_valid_russian_phone()
    {
        $this->assertTrue(isValidRussianPhone('+79219245228'));
        $this->assertTrue(isValidRussianPhone('89219245228'));
        $this->assertTrue(isValidRussianPhone('9219245228'));
        
        // Некорректные номера
        $this->assertFalse(isValidRussianPhone('invalid'));
        $this->assertFalse(isValidRussianPhone('1234567890'));
        $this->assertFalse(isValidRussianPhone(''));
    }
}
```

## Тесты безопасности

### Файл: tests/Feature/Auth/SecurityTest.php

### Новые тесты:
```php
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
```

## Тесты производительности

### Файл: tests/Feature/Auth/PerformanceTest.php

### Новые тесты:
```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_performance()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        // Измеряем время выполнения входа
        $start = microtime(true);
        
        $response = $this->post('/login', [
            'phone' => '+7(921)924-52-28',
            'password' => 'password',
        ]);
        
        $end = microtime(true);
        $duration = ($end - $start) * 1000; // в миллисекундах

        // Вход должен занимать менее 500 мс
        $this->assertLessThan(500, $duration);
        
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_password_reset_performance()
    {
        $user = User::factory()->create([
            'phone' => '9219245228',
        ]);

        // Измеряем время выполнения запроса сброса пароля
        $start = microtime(true);
        
        $response = $this->post('/forgot-password', [
            'phone' => '+7(921)924-52-28',
        ]);
        
        $end = microtime(true);
        $duration = ($end - $start) * 1000; // в миллисекундах

        // Запрос сброса пароля должен занимать менее 1000 мс
        $this->assertLessThan(1000, $duration);
        
        $response->assertSessionHas('status');
    }
}
```

## Запуск тестов

### Команды для запуска тестов:
```bash
# Запуск всех тестов аутентификации
php artisan test --filter Auth

# Запуск тестов входа
php artisan test --filter AuthenticationTest

# Запуск тестов регистрации
php artisan test --filter RegistrationTest

# Запуск тестов сброса пароля
php artisan test --filter PasswordResetTest

# Запуск тестов валидации номера телефона
php artisan test --filter PhoneValidationTest

# Запуск тестов безопасности
php artisan test --filter SecurityTest

# Запуск тестов производительности
php artisan test --filter PerformanceTest
```

## Покрытие тестами

### Цели покрытия:
1. 100% покрытие функций аутентификации
2. 100% покрытие функций регистрации
3. 100% покрытие функций сброса пароля
4. 100% покрытие helper функций работы с номером телефона
5. 100% покрытие функций безопасности

### Генерация отчета о покрытии:
```bash
# Установка phpunit/php-code-coverage
composer require --dev phpunit/php-code-coverage

# Запуск тестов с генерацией отчета о покрытии
php artisan test --coverage-html coverage
```

## Непрерывная интеграция

### Конфигурация GitHub Actions:
```yaml
name: Authentication Tests

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v2

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, pdo, sqlite, gd, zip
        coverage: xdebug

    - name: Install dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

    - name: Create database
      run: touch database/database.sqlite

    - name: Run authentication tests
      run: php artisan test --filter Auth --coverage-clover coverage.xml

    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v1
      with:
        file: ./coverage.xml
        flags: authentication