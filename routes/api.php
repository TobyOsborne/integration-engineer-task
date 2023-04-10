<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriberController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('subscribers', SubscriberController::class)->except([
    'show'
]);
Route::get('subscribers', [SubscriberController::class, 'list'])->name('subscribers.list');
Route::match(['put', 'post'], 'settings', [SettingsController::class, 'update'])->name('settings.update');
