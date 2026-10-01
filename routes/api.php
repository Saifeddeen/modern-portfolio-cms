<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\TechnologyController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SkillController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/settings', [SiteSettingController::class, 'index'])->name('api.settings.index');
Route::get('/technologies', [TechnologyController::class, 'index'])->name('api.technologies.index');
Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
Route::get('/skills', [SkillController::class, 'index'])->name('api.skills.index');
