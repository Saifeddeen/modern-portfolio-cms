<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiteSettingController;

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

// We will add more API routes here as we build new features (Projects, Experience, etc.)