<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\AppUser\appAuthController;
use App\Http\Controllers\AppUser\AppUsersController;
use App\Http\Controllers\AppUser\BookingController;
use App\Http\Controllers\AppUser\GeneralController;
use App\Http\Controllers\AppUser\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'app-user'
], function ($router) {
    //auth
    Route::post('/logout', [appAuthController::class, 'logout']);
    Route::post('/login', [appAuthController::class, 'login']);
    Route::post('/register', [appAuthController::class, 'register']);

    //booking
    Route::get('/user/bookings', [BookingController::class, 'userBookings']);
    Route::post('booking', [BookingController::class, 'bookService']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancelBooking']);

    //General
    Route::get('/services', [GeneralController::class, 'getAllServices']);
    Route::get('/contact-us', [GeneralController::class, 'getContactUs']);
    Route::get('/about-us', [GeneralController::class, 'getAboutUs']);
    Route::get('/question', [GeneralController::class, 'getQuestion']);
    //suscriptions
    Route::get('/suscriptions/{id}', [SubscriptionController::class,'show']);
    Route::get('/suscriptions', [SubscriptionController::class,'index']);
   

  
});


require __DIR__ . '/dashboard.php';