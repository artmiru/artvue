<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Teacher;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Получаем активных учителей с данными пользователей
        $teachers = Teacher::with('user')
            ->whereNull('deleted_at')
            ->get();
        
        return Inertia::render('Home', [
            'teachers' => $teachers
        ]);
    }
}