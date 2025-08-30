<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/courses', function () {
    $courses = app(CourseController::class)->index(request());
    return Inertia::render('Courses', ['courses' => $courses->getData()]);
})->name('courses');

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
