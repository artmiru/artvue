<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\PhoneHelper;
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
    use PhoneHelper;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.unique' => 'Этот номер телефона уже зарегистрирован',
            'phone.required' => 'Номер телефона обязателен для заполнения',
        ]);

        // Извлечение 10 цифр из номера телефона
        $phone = $this->extractDigits($request->phone);
        
        // Проверка корректности номера телефона
        if (!$phone || !$this->isValidRussianPhone($phone)) {
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

        $user = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
