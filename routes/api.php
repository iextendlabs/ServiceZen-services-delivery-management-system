<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController\{
    StaffAppController,
    DriverAppController
};
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('orders', [StaffAppController::class, 'orders']);
Route::post('login', [StaffAppController::class, 'login']);
Route::post('addOrderComment', [StaffAppController::class, 'addComment']);
Route::post('cashCollection', [StaffAppController::class, 'cashCollection']);
Route::post('orderStatusUpdate', [StaffAppController::class, 'orderStatusUpdate']);
Route::post('rescheduleOrder', [StaffAppController::class, 'rescheduleOrder']);
Route::get('timeSlots', [StaffAppController::class, 'timeSlots']);

// Driver app       

Route::get('driverAppOrders', [DriverAppController::class, 'orders']);
Route::get('driverAppUser', [DriverAppController::class, 'user']);
Route::get('driverAppOrderStatusUpdate/{order}', [DriverAppController::class, 'orderDriverStatusUpdate']);