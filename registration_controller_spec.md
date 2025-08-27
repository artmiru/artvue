# Спецификация обновления контроллера регистрации

## Общие требования

1. Заменить валидацию email на phone
2. Обновить создание пользователя с использованием phone вместо email
3. Добавить валидацию уникальности номера телефона
4. Обновить сообщения об ошибках
5. Сохранить существующую логику регистрации

## Контроллер регистрации (RegisteredUserController)

### Файл: app/Http/Controllers/Auth/RegisteredUserController.php

### Изменения:
1. Обновить метод store() для работы с номером телефона
2. Добавить валидацию номера телефона
3. Добавить извлечение 10 цифр из номера телефона
4. Обновить создание пользователя
5. Добавить проверку уникальности номера телефона

### Новый код:
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Валидация входных данных
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.unique' => 'Этот номер телефона уже зарегистрирован',
            'phone.required' => 'Номер телефона обязателен для заполнения',
        ]);

        // Извлечение 10 цифр из номера телефона
        $phone = extractDigits($validated['phone']);
        
        // Проверка корректности номера телефона
        if (!$phone || !isValidRussianPhone($phone)) {
            return back()->withErrors([
                'phone' => 'Некорректный номер телефона',
            ]);
        }

        // Проверка уникальности номера телефона (дополнительная проверка)
        if (User::where('phone', $phone)->exists()) {
            return back()->withErrors([
                'phone' => 'Этот номер телефона уже зарегистрирован',
            ]);
        }

        // Создание пользователя
        $user = User::create([
            'name' => $validated['name'],
            'phone' => $phone,
            'password' => Hash::make($validated['password']),
        ]);

        // Генерация события регистрации
        event(new Registered($user));

        // Автоматический вход пользователя
        Auth::login($user);

        // Перенаправление на dashboard
        return to_route('dashboard');
    }
}
```

## Вспомогательные функции

### Файл: app/Helpers/PhoneHelper.php

```php
<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Извлекает 10 цифр из номера телефона
     * @param string $phone - Номер телефона в любом формате
     * @return string|null - 10 цифр номера телефона или null
     */
    public static function extractDigits($phone)
    {
        if (!$phone) return null;
        
        // Извлекаем только цифры
        $digits = preg_replace('/\D/', '', $phone);
        
        // Если начинается с 7 или 8, убираем первую цифру
        if (strlen($digits) === 11 && ($digits[0] === '7' || $digits[0] === '8')) {
            return substr($digits, 1);
        }
        
        // Если уже 10 цифр, возвращаем как есть
        if (strlen($digits) === 10) {
            return $digits;
        }
        
        // Если меньше 10 цифр, возвращаем null
        return null;
    }

    /**
     * Проверяет корректность номера телефона
     * @param string $phone - Номер телефона в любом формате
     * @return bool - true, если номер корректный
     */
    public static function validatePhone($phone)
    {
        if (!$phone) return false;
        
        $digits = self::extractDigits($phone);
        if (!$digits) return false;
        
        // Проверяем, что номер начинается с допустимого кода оператора
        $validPrefixes = [
            '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'
        ];
        
        foreach ($validPrefixes as $prefix) {
            if (strpos($digits, $prefix) === 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Форматирует номер телефона для отображения
     * @param string $phone - Номер телефона в любом формате
     * @return string - Отформатированный номер телефона
     */
    public static function formatPhone($phone)
    {
        if (!$phone) return '';
        
        $digits = self::extractDigits($phone);
        if (!$digits) return $phone;
        
        // Форматируем как +7 (921) 924-52-28
        return '+7 (' . substr($digits, 0, 3) . ') ' . substr($digits, 3, 3) . '-' . substr($digits, 6, 2) . '-' . substr($digits, 8, 2);
    }

    /**
     * Проверяет, является ли номер действительным российским номером
     * @param string $phone - Номер телефона в любом формате
     * @return bool - true, если номер является действительным российским номером
     */
    public static function isValidRussianPhone($phone)
    {
        return self::validatePhone($phone);
    }
}
```

## Обновление провайдера сервисов

### Файл: app/Providers/AppServiceProvider.php

Добавить регистрацию helper функций:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\PhoneHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрация helper функций
        $this->app->singleton('phone', function () {
            return new PhoneHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

## Добавление глобальных helper функций

### Файл: app/helpers.php

```php
<?php

if (!function_exists('extractDigits')) {
    /**
     * Извлекает 10 цифр из номера телефона
     * @param string $phone - Номер телефона в любом формате
     * @return string|null - 10 цифр номера телефона или null
     */
    function extractDigits($phone)
    {
        return \App\Helpers\PhoneHelper::extractDigits($phone);
    }
}

if (!function_exists('validatePhone')) {
    /**
     * Проверяет корректность номера телефона
     * @param string $phone - Номер телефона в любом формате
     * @return bool - true, если номер корректный
     */
    function validatePhone($phone)
    {
        return \App\Helpers\PhoneHelper::validatePhone($phone);
    }
}

if (!function_exists('formatPhone')) {
    /**
     * Форматирует номер телефона для отображения
     * @param string $phone - Номер телефона в любом формате
     * @return string - Отформатированный номер телефона
     */
    function formatPhone($phone)
    {
        return \App\Helpers\PhoneHelper::formatPhone($phone);
    }
}

if (!function_exists('isValidRussianPhone')) {
    /**
     * Проверяет, является ли номер действительным российским номером
     * @param string $phone - Номер телефона в любом формате
     * @return bool - true, если номер является действительным российским номером
     */
    function isValidRussianPhone($phone)
    {
        return \App\Helpers\PhoneHelper::isValidRussianPhone($phone);
    }
}
```

## Обновление composer.json

Добавить автозагрузку helper функций:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        },
        "files": [
            "app/helpers.php"
        ]
    }
}
```

## Тестирование

### Создать тесты для проверки:
1. Валидации номера телефона
2. Извлечения 10 цифр из номера телефона
3. Создания пользователя с номером телефона
4. Обработки дубликатов номеров телефонов
5. Обработки некорректных номеров телефонов

## Безопасность

### Меры безопасности:
1. Валидация входных данных
2. Проверка уникальности номера телефона
3. Защита от SQL-инъекций
4. Логирование регистраций
5. Ограничение количества регистраций с одного IP

### Ограничение регистраций:
```php
// В RegisteredUserController
public function store(Request $request): RedirectResponse
{
    // Ограничение количества регистраций с одного IP
    $this->validateRegistrationRateLimit($request);
    
    // Остальная логика...
}

protected function validateRegistrationRateLimit(Request $request)
{
    $key = 'registration.' . $request->ip();
    $maxAttempts = 5;
    $decayMinutes = 60;
    
    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
        $seconds = RateLimiter::availableIn($key);
        
        throw ValidationException::withMessages([
            'phone' => "Слишком много регистраций. Попробуйте через {$seconds} секунд.",
        ]);
    }
    
    RateLimiter::hit($key);
}
```

## Логирование

### Логирование регистраций:
```php
// В RegisteredUserController
use Illuminate\Support\Facades\Log;

public function store(Request $request): RedirectResponse
{
    // Логирование регистрации
    Log::info('User registration attempt', [
        'phone' => $request->phone,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    
    // Остальная логика...
}