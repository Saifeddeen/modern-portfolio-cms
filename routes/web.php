<?php

use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\TechnologyController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\SkillController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('technologies', TechnologyController::class)->except(['show', 'create', 'edit']);
    Route::resource('services', ServiceController::class)->except(['show', 'create', 'edit']);
    Route::resource('skills', SkillController::class)->except(['show', 'create', 'edit']);
    Route::resource('projects', ProjectController::class)->except(['show', 'create', 'edit']);
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project}/gallery', [ProjectController::class, 'storeGallery'])->name('projects.gallery.store');
});

require __DIR__ . '/auth.php';
