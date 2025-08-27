# Спецификация обновления контроллера сброса пароля

## Общие требования

1. Заменить валидацию email на phone
2. Обновить логику поиска пользователя по телефону
3. Добавить отправку SMS с кодом подтверждения
4. Обновить сообщения об ошибках
5. Сохранить существующую логику сброса пароля

## Контроллер сброса пароля (PasswordResetLinkController)

### Файл: app/Http/Controllers/Auth/PasswordResetLinkController.php

### Изменения:
1. Обновить метод store() для работы с номером телефона
2. Добавить валидацию номера телефона
3. Добавить извлечение 10 цифр из номера телефона
4. Обновить логику поиска пользователя
5. Добавить отправку SMS с кодом подтверждения

### Новый код:
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Валидация входных данных
        $request->validate([
            'phone' => 'required|string',
        ], [
            'phone.required' => 'Номер телефона обязателен для заполнения',
        ]);

        // Извлечение 10 цифр из номера телефона
        $phone = extractDigits($request->phone);
        
        // Проверка корректности номера телефона
        if (!$phone || !isValidRussianPhone($phone)) {
            return back()->withErrors([
                'phone' => 'Некорректный номер телефона',
            ]);
        }

        // Поиск пользователя по номеру телефона
        $user = User::where('phone', $phone)->first();

        // Для безопасности возвращаем успех даже если пользователя нет
        if (!$user) {
            return back()->with('status', 'Если номер телефона зарегистрирован, мы отправим код подтверждения');
        }

        // Создание токена сброса пароля
        $token = Password::getRepository()->create($user);

        // Отправка SMS с токеном
        // Здесь должна быть реализация отправки SMS через выбранного провайдера
        $this->sendSms($user->phone, "Ваш код для сброса пароля: {$token}");

        // Логирование отправки SMS
        \Illuminate\Support\Facades\Log::info('Password reset SMS sent', [
            'phone' => $user->phone,
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return back()->with('status', 'Мы отправили код подтверждения на ваш номер телефона');
    }

    /**
     * Отправка SMS с кодом подтверждения
     * @param string $phone - Номер телефона
     * @param string $message - Сообщение
     * @return bool - Результат отправки
     */
    protected function sendSms($phone, $message)
    {
        // Здесь должна быть реализация отправки SMS через выбранного провайдера
        // Например, через SMS.ru, Twilio, AWS SNS и т.д.
        
        try {
            // Пример для SMS.ru (нужно установить соответствующий пакет)
            // $sms = new \SMSRU\Client(env('SMSRU_API_ID'));
            // $result = $sms->send($phone, $message);
            
            // Пример для Twilio (нужно установить соответствующий пакет)
            // $twilio = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            // $twilio->messages->create($phone, [
            //     'from' => env('TWILIO_FROM'),
            //     'body' => $message
            // ]);
            
            // Для демонстрации возвращаем true
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send SMS', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
}
```

## Обновление репозитория токенов

### Файл: app/Repositories/PhoneTokenRepository.php

Создать новый репозиторий для работы с токенами сброса пароля по телефону:

```php
<?php

namespace App\Repositories;

use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PhoneTokenRepository
{
    /**
     * Создание токена сброса пароля
     * @param mixed $user - Пользователь
     * @return string - Токен
     */
    public function create($user)
    {
        $token = $this->createNewToken();
        
        PasswordResetToken::updateOrCreate(
            ['phone' => $user->phone],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );
        
        return $token;
    }

    /**
     * Проверка существования токена
     * @param mixed $user - Пользователь
     * @param string $token - Токен
     * @return bool - Результат проверки
     */
    public function exists($user, $token)
    {
        $record = PasswordResetToken::where('phone', $user->phone)->first();
        
        if (!$record) {
            return false;
        }
        
        return hash_equals($record->token, $token) && !$this->tokenExpired($record->created_at);
    }

    /**
     * Удаление токена
     * @param mixed $user - Пользователь
     */
    public function delete($user)
    {
        PasswordResetToken::where('phone', $user->phone)->delete();
    }

    /**
     * Удаление просроченных токенов
     */
    public function deleteExpired()
    {
        PasswordResetToken::where('created_at', '<', Carbon::now()->subHours(24))->delete();
    }

    /**
     * Создание нового токена
     * @return string - Токен
     */
    protected function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));
    }

    /**
     * Проверка истечения срока действия токена
     * @param Carbon $createdAt - Время создания
     * @return bool - Результат проверки
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addHours(24)->isPast();
    }
}
```

## Обновление провайдера паролей

### Файл: app/Providers/AuthServiceProvider.php

Обновить провайдер для работы с телефоном:

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Password;
use App\Repositories\PhoneTokenRepository;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Обновление репозитория токенов
        Password::broker()->setRepository(new PhoneTokenRepository());
        
        // Обновление URL для сброса пароля
        Password::createUrlUsing(function ($user, string $token) {
            return url(route('password.reset', [
                'token' => $token,
                'phone' => $user->phone,
            ], false));
        });
    }
}
```

## Обновление конфигурации

### Файл: config/auth.php

Обновить конфигурацию для работы с телефоном:

```php
<?php

return [
    // ... другие настройки

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    // ... другие настройки
];
```

## Обновление модели пользователя

### Файл: app/Models/User.php

Добавить методы для работы с сбросом пароля по телефону:

```php
<?php

namespace App\Models;

// ... другие импорты
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements CanResetPasswordContract
{
    // ... другие трейты
    use CanResetPassword;

    /**
     * Get the phone address used for password reset notifications.
     *
     * @return string
     */
    public function routeNotificationForPasswordReset()
    {
        return $this->phone;
    }

    /**
     * Get the password reset token attribute.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->phone;
    }
}
```

## Тестирование

### Создать тесты для проверки:
1. Валидации номера телефона
2. Извлечения 10 цифр из номера телефона
3. Поиска пользователя по номеру телефона
4. Создания токена сброса пароля
5. Отправки SMS с кодом подтверждения
6. Обработки несуществующих номеров телефонов
7. Обработки просроченных токенов

## Безопасность

### Меры безопасности:
1. Валидация входных данных
2. Защита от брутфорса (ограничение попыток)
3. Логирование всех попыток сброса пароля
4. Шифрование токенов
5. Ограничение времени жизни токена

### Ограничение попыток:
```php
// В PasswordResetLinkController
public function store(Request $request): RedirectResponse
{
    // Ограничение попыток (rate limiting)
    $this->validatePhoneRateLimit($request);
    
    // Остальная логика...
}

protected function validatePhoneRateLimit(Request $request)
{
    $phone = extractDigits($request->phone);
    
    if (!$phone) {
        return;
    }
    
    $key = 'password.reset.' . $phone;
    $maxAttempts = 3;
    $decayMinutes = 15;
    
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
        $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
        
        throw \Illuminate\Validation\ValidationException::withMessages([
            'phone' => "Слишком много попыток. Попробуйте через {$seconds} секунд.",
        ]);
    }
    
    \Illuminate\Support\Facades\RateLimiter::hit($key);
}
```

## Логирование

### Логирование действий:
```php
// В PasswordResetLinkController
use Illuminate\Support\Facades\Log;

public function store(Request $request): RedirectResponse
{
    // Логирование попытки сброса пароля
    Log::info('Password reset requested', [
        'phone' => $request->phone,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    
    // Остальная логика...
}
```

## Интеграция с SMS-провайдером

### Пример для SMS.ru:
1. Установить пакет: `composer require smsru/php-sdk`
2. Добавить в .env: `SMSRU_API_ID=ваш_api_id`
3. Обновить метод sendSms:

```php
protected function sendSms($phone, $message)
{
    try {
        $sms = new \SMSRU\Client(env('SMSRU_API_ID'));
        $result = $sms->send($phone, $message);
        
        return $result->status === 'OK';
    } catch (\Exception $e) {
        Log::error('Failed to send SMS via SMS.ru', [
            'phone' => $phone,
            'error' => $e->getMessage(),
        ]);
        
        return false;
    }
}
```

### Пример для Twilio:
1. Установить пакет: `composer require twilio/sdk`
2. Добавить в .env:
   ```
   TWILIO_SID=ваш_sid
   TWILIO_TOKEN=ваш_token
   TWILIO_FROM=ваш_номер
   ```
3. Обновить метод sendSms:

```php
protected function sendSms($phone, $message)
{
    try {
        $twilio = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        $twilio->messages->create($phone, [
            'from' => env('TWILIO_FROM'),
            'body' => $message
        ]);
        
        return true;
    } catch (\Exception $e) {
        Log::error('Failed to send SMS via Twilio', [
            'phone' => $phone,
            'error' => $e->getMessage(),
        ]);
        
        return false;
    }
}