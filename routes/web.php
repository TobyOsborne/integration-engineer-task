<?php
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriberController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [SubscriberController::class, 'index'])->middleware('setup.required')->name('subscribers');
Route::get('/subscribers/{id}', [SubscriberController::class, 'edit'])
    ->middleware('setup.required')
    ->name('subscribers.edit');
;
Route::get('/subscribers', [SubscriberController::class, 'create'])
    ->middleware('setup.required')
    ->name('subscribers.create');

Route::get('settings', [SettingsController::class, 'index'])->name('settings');
