# Спецификация обновления таблицы password_reset_tokens

## Текущая структура таблицы

### Таблица: password_reset_tokens

| Поле | Тип | Описание |
|------|-----|----------|
| email | string (primary) | Email пользователя |
| token | string | Токен сброса пароля |
| created_at | timestamp | Время создания токена |

## Новая структура таблицы

### Таблица: password_reset_tokens

| Поле | Тип | Описание |
|------|-----|----------|
| phone | string (primary) | Номер телефона пользователя (10 цифр) |
| token | string | Токен сброса пароля |
| created_at | timestamp | Время создания токена |

## Миграция

### Название файла
`2025_08_27_0000_update_password_reset_tokens_table.php`

### Описание изменений
1. Удалить поле `email`
2. Добавить поле `phone` с типом string и длиной 10 символов
3. Сделать поле `phone` первичным ключом
4. Обновить индексы

### Код миграции
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone', 10)->primary()->comment('10 цифр номера телефона');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            
            // Индекс для ускорения поиска
            $table->index('phone', 'password_reset_tokens_phone_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        
        // Восстановление старой структуры
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
};
```

## Обновление модели

### Файл: app/Models/PasswordResetToken.php

Создать новую модель для работы с таблицей password_reset_tokens:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_reset_tokens';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'phone';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone',
        'token',
        'created_at',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
```

## Обновление фасада Password

### Файл: app/Providers/AuthServiceProvider.php

Обновить методы для работы с телефоном вместо email:

```php
// В методе boot() или в отдельном сервис-провайдере
Password::createUrlUsing(function ($user, string $token) {
    // Создание URL для сброса пароля по телефону
    return url(route('password.reset', [
        'token' => $token,
        'phone' => $user->phone,
    ], false));
});

Password::sendPasswordResetNotification(function ($user, string $token) {
    // Отправка уведомления о сбросе пароля через SMS
    // Вместо отправки email
});
```

## Обновление уведомлений

### Файл: app/Notifications/ResetPassword.php

Обновить уведомление для отправки через SMS:

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\NexmoMessage; // Для SMS через Nexmo/Vonage

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        // Использовать SMS вместо email
        return ['nexmo']; // или другой драйвер SMS
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content("Ваш код для сброса пароля: {$this->token}");
    }
}
```

## Обновление контроллера сброса пароля

### Файл: app/Http/Controllers/Auth/PasswordResetLinkController.php

Обновить методы для работы с телефоном:

```php
public function store(Request $request)
{
    $request->validate([
        'phone' => 'required|string',
    ]);

    // Извлечение 10 цифр из номера телефона
    $phone = extractDigits($request->phone);
    
    if (!$phone || !isValidRussianPhone($phone)) {
        return back()->withErrors([
            'phone' => 'Некорректный номер телефона',
        ]);
    }

    // Поиск пользователя по номеру телефона
    $user = User::where('phone', $phone)->first();

    if (!$user) {
        // Для безопасности возвращаем успех даже если пользователя нет
        return back()->with('status', 'Если номер телефона зарегистрирован, мы отправим код подтверждения');
    }

    // Создание токена сброса пароля
    $token = Password::getRepository()->create($user);

    // Отправка SMS с токеном
    $user->notify(new ResetPassword($token));

    return back()->with('status', 'Мы отправили код подтверждения на ваш номер телефона');
}
```

## Обновление модели пользователя

### Файл: app/Models/User.php

Добавить методы для работы с сбросом пароля по телефону:

```php
/**
 * Get the phone address used for password reset notifications.
 *
 * @return string
 */
public function routeNotificationForPasswordReset()
{
    return $this->phone;
}
```

## Тестирование

### Создать тесты для проверки:
1. Создания токена сброса пароля по телефону
2. Отправки SMS с токеном
3. Валидации номера телефона
4. Обработки несуществующих номеров
5. Обратной совместимости (если необходимо)

## Безопасность

### Меры безопасности:
1. Ограничение количества попыток сброса пароля
2. Логирование всех попыток сброса
3. Использование HTTPS для всех запросов
4. Валидация входных данных
5. Защита от брутфорса
6. Ограничение времени жизни токена

### Ограничение попыток:
```php
// В PasswordResetLinkController
public function store(Request $request)
{
    // Ограничение попыток (rate limiting)
    if ($request->user()) {
        $this->validatePhoneRateLimit($request);
    }
    
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
    
    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
        $seconds = RateLimiter::availableIn($key);
        
        throw ValidationException::withMessages([
            'phone' => "Слишком много попыток. Попробуйте через {$seconds} секунд.",
        ]);
    }
    
    RateLimiter::hit($key);
}
```

## Логирование

### Логирование действий:
1. Все попытки сброса пароля
2. Успешные отправки SMS
3. Ошибки отправки SMS
4. Использование токенов

### Пример логирования:
```php
// В PasswordResetLinkController
Log::info('Password reset requested', [
    'phone' => $request->phone,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);