<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/courses/{slug}', function ($slug) {
    return Inertia::render('courses/Show', ['slug' => $slug]);
})->name('courses.show');

Route::get('/{slug}', function ($slug) {
    // Проверяем, существует ли курс с таким slug
    $course = app(App\Models\Course::class)->where('slug', $slug)->first();
    
    if ($course) {
        return Inertia::render('courses/Show', ['slug' => $slug]);
    }
    
    // Если курс не найден, возвращаем 404
    abort(404);
})->where('slug', '^(?!api|settings|dashboard|login|register|forgot-password|reset-password|confirm-password|verify-email|up|storage).*$');

Route::get('/kursy-risovaniya', function () {
    return Inertia::render('courses/Drawing');
})->name('drawing');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// API routes for courses
Route::get('/api/courses', [CourseController::class, 'index'])->name('api.courses.index');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
