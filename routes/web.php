<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Test
Route::get('/test', function () {
    phpinfo();
});

// Public
Route::get('/home', function () {
    return view('index');
});
Route::get('/menu', [MealController::class, 'menu']);

Route::get('/customers/verify', [CustomerController::class, 'verify']);
Route::get('/customers/register', [CustomerController::class, 'register']);
Route::post('/customers/register', [CustomerController::class, 'doRegister']);
Route::get('/customers/profile', [CustomerController::class, 'profile']);
Route::post('/customers/profile', [CustomerController::class, 'doProfile']);

Route::get('/login', [SessionController::class, 'login']);
Route::post('/login', [SessionController::class, 'doLogin']);
Route::get('/google', [SessionController::class, 'googleLogin']);
Route::get('/google/callback', [SessionController::class, 'googleLoginCallback']);
Route::get('/logout', [SessionController::class, 'logout']);

// Staff
Route::put('/meals/{id}/toggleAvailability', [MealController::class, 'toggleAvailability']);
Route::resource('/meals', MealController::class);
Route::get('/tables/qr', [QrCodeController::class, 'show']);
Route::get('/tables/arrange', [TableController::class, 'arrange']);
Route::resource('/tables', TableController::class);

// Admin
Route::resource('/customers', CustomerController::class);
Route::resource('/staff', StaffController::class);