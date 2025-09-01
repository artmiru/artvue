<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MasterClassController;
use App\Http\Controllers\GiftCertificateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes for courses
Route::get('/courses/{slug}', function ($slug) {
    return Inertia::render('courses/Show', ['slug' => $slug]);
})->name('courses.show');

// Routes for master classes
Route::get('/mk/{slug}', function ($slug) {
    return Inertia::render('master-classes/Show', ['slug' => $slug]);
})->name('master-classes.show');

// Special route for /mk - redirect to master classes
Route::get('/mk', function () {
    return Inertia::render('master-classes/Index');
})->name('master-classes.list');

// Routes for gift certificates
Route::get('/gift-certificates/{code}', function ($code) {
    return Inertia::render('gift-certificates/Show', ['code' => $code]);
})->name('gift-certificates.show');

// Routes for products
Route::get('/products/{id}', function ($id) {
    return Inertia::render('products/Show', ['id' => $id]);
})->name('products.show');

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
    return Inertia::render('courses/Show', ['slug' => 'kursy-risovaniya']);
})->name('drawing');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// API routes
Route::get('/api/courses', [CourseController::class, 'index'])->name('api.courses.index');
Route::get('/api/master-classes', [MasterClassController::class, 'index'])->name('api.master-classes.index');
Route::get('/api/gift-certificates', [GiftCertificateController::class, 'index'])->name('api.gift-certificates.index');
Route::get('/api/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/api/teachers', [\App\Http\Controllers\TeacherController::class, 'index'])->name('api.teachers.index');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
