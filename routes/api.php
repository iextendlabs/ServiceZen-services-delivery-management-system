<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController\{
    StaffAppController2,
    StaffAppController,
    DriverAppController,
    ChatController,
    CustomerController
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


Route::get('orders', [StaffAppController2::class, 'orders']);
Route::get('ordersV2', [StaffAppController::class, 'orders']);
Route::post('login', [StaffAppController2::class, 'login']);
Route::post('addOrderComment', [StaffAppController2::class, 'addComment']);
Route::post('cashCollection', [StaffAppController2::class, 'cashCollection']);
Route::post('orderStatusUpdate', [StaffAppController2::class, 'orderStatusUpdate']);
Route::post('driverOrderStatusUpdate', [StaffAppController2::class, 'driverOrderStatusUpdate']);
Route::post('rescheduleOrder', [StaffAppController2::class, 'rescheduleOrder']);
Route::get('timeSlots', [StaffAppController2::class, 'timeSlots']);
Route::get('orderChat', [ChatController::class, 'orderChat']);
Route::post('addOrderChat', [ChatController::class, 'addOrderChat']);
Route::get('notification', [StaffAppController2::class, 'notification']);
Route::get('driverOrders', [DriverAppController::class, 'orders']);
Route::post('driverLogin', [DriverAppController::class, 'login']);
Route::get('driverOrderStatusUpdate/{order}', [DriverAppController::class, 'orderDriverStatusUpdate']);
Route::post('updateToken', [DriverAppController::class, 'updateToken']);
Route::post('addShortHoliday', [StaffAppController2::class, 'addShortHoliday']);

// customer App
Route::post('customerLogin', [CustomerController::class, 'login']);
Route::post('customerSignup', [CustomerController::class, 'signup']);
Route::get('appIndex', [CustomerController::class, 'index']);
Route::get('availableTimeSlot', [CustomerController::class, 'availableTimeSlot']);
Route::post('addOrder', [CustomerController::class, 'addOrder']);
Route::get('getOrders', [CustomerController::class, 'getOrders']);
Route::get('getZones', [CustomerController::class, 'getZones']);
Route::get('editOrder', [CustomerController::class, 'editOrder']);
Route::get('filterServices', [CustomerController::class, 'filterServices']);
Route::get('getServiceDetails', [CustomerController::class, 'getServiceDetails']);
Route::post('updateOrder', [CustomerController::class, 'updateOrder']);
Route::post('updateCustomerInfo', [CustomerController::class, 'updateCustomerInfo']);
Route::post('applyCouponAffiliate', [CustomerController::class, 'applyCouponAffiliate']);
Route::get('order-download-pdf/{id}', [CustomerController::class, 'downloadPDF'])->name('order.downloadPDF');
Route::post('writeReview', [CustomerController::class, 'writeReview']);
Route::get('getCustomerCoupon', [CustomerController::class, 'getCustomerCoupon']);
Route::get('customerNotification', [CustomerController::class, 'notification']);
Route::get('customerChat', [CustomerController::class, 'getChat']);
Route::post('addCustomerChat', [CustomerController::class, 'addChat']);
Route::post('passwordReset', [CustomerController::class, 'passwordReset']);
Route::get('staff/{id}', [CustomerController::class, 'staff'])->name('staff');
Route::get('deleteAccountMail', [CustomerController::class, 'deleteAccountMail']);
Route::get('subCategories', [CustomerController::class, 'getSubCategories']);
Route::get('appOffer', [CustomerController::class, 'getOffer']);
Route::get('checkUser', [CustomerController::class, 'checkUser']);
Route::post('orderIssueMail', [CustomerController::class, 'orderIssueMail']);