<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

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