<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ReservationController;
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

// Public
Route::get('/home', function () {
    return view('index');
});
Route::get('/menu', [MealController::class, 'menu']);
Route::get('/track', [TableController::class, 'track']);
Route::resource('/reservations', ReservationController::class)->only(['create']);

Route::post('/carts/session', [CartController::class, 'session']);
Route::resource('/carts', CartController::class)->only(['index', 'store', 'update', 'destroy']);

Route::get('/orders/view', [OrderController::class, 'view']);
Route::resource('/orders', OrderController::class)->only(['store', 'update']);

Route::get('/customers/verify', [CustomerController::class, 'verify']);
Route::get('/customers/register', [CustomerController::class, 'register']);
Route::post('/customers/register', [CustomerController::class, 'doRegister']);

Route::get('/login', [SessionController::class, 'login']);
Route::post('/login', [SessionController::class, 'doLogin']);
Route::get('/google', [SessionController::class, 'googleLogin']);
Route::get('/google/callback', [SessionController::class, 'googleLoginCallback']);
Route::get('/logout', [SessionController::class, 'logout']);


// Customer
Route::group(['middleware' => 'auth.custom:customer'], function () {
    // Manage reservations
    Route::get('/reservations/session', [ReservationController::class, 'session']);
    Route::get('/reservations/view', [ReservationController::class, 'view']);
    Route::resource('/reservations', ReservationController::class)->only(['store', 'update', 'destroy']);

    // Manage profile
    Route::get('/customers/profile', [CustomerController::class, 'profile']);
    Route::post('/customers/profile', [CustomerController::class, 'doProfile']);
});


// Staff
Route::group(['middleware' => 'auth.custom:staff'], function () {
    // Manage tables
    Route::get('/tables/qr', [QrCodeController::class, 'download']);
    Route::get('/tables/arrange', [TableController::class, 'arrange']);
    Route::resource('/tables', TableController::class)->only(['index', 'store', 'show']);

    // Manage orders
    Route::resource('/orders', OrderController::class)->only(['index']);

    // Manage reservations
    Route::post('/tables/active', [TableController::class, 'active']);
    Route::post('/reservations/assign', [ReservationController::class, 'assign']);
    Route::resource('/reservations', ReservationController::class)->only(['index']);

    // Manage meals
    Route::put('/meals/{id}/toggleAvailability', [MealController::class, 'toggleAvailability']);
    Route::resource('/meals', MealController::class);
});


// Admin
Route::group(['middleware' => 'auth.custom:staff,admin'], function () {
    // Manage customers
    Route::resource('/customers', CustomerController::class);

    // Manage staff
    Route::resource('/staff', StaffController::class);
});






// Customer
// Route::get('/reservations/view', [ReservationController::class, 'view']);
// Route::get('/reservations/session', [ReservationController::class, 'session']);
// Route::get('/customers/profile', [CustomerController::class, 'profile']);
// Route::post('/customers/profile', [CustomerController::class, 'doProfile']);

// Staff
// Route::post('/reservations/assign', [ReservationController::class, 'assign']);
// Route::post('/tables/active', [TableController::class, 'active']);
// Route::get('/tables/qr', [QrCodeController::class, 'download']);
// Route::get('/tables/arrange', [TableController::class, 'arrange']);
// Route::resource('/tables', TableController::class);
// Route::put('/meals/{id}/toggleAvailability', [MealController::class, 'toggleAvailability']);
// Route::resource('/meals', MealController::class);
// Route::resource('/orders', OrderController::class);
// Route::resource('/reservations', ReservationController::class);

// Admin
// Route::resource('/customers', CustomerController::class);
// Route::resource('/staff', StaffController::class);