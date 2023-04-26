<?php

use App\Http\Controllers\AffiliateController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceStaffController;
use App\Http\Controllers\Site\ServiceAppointmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Site\CustomerAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffZoneController;
use App\Http\Controllers\TransactionController;
use App\Models\StaffZone;

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


Auth::routes();

Route::get('/admin', [HomeController::class, 'index'])->name('home');
  
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('serviceStaff', ServiceStaffController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::resource('serviceCategories', ServiceCategoryController::class);
    Route::resource('affiliates', AffiliateController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('staffZones', StaffZoneController::class);

    Route::post('serviceFilter', [ServiceController::class, 'filter']); 
    Route::post('appointmentFilter', [AppointmentController::class, 'filter']); 
    Route::post('orderFilter', [OrderController::class, 'filter']); 
    Route::post('serviceStaffFilter', [ServiceStaffController::class, 'filter']); 
    Route::post('customerFilter', [CustomerController::class, 'filter']); 
    Route::post('affiliateFilter', [AffiliateController::class, 'filter']); 
    Route::post('userFilter', [UserController::class, 'filter']); 
    Route::get('appointmentDetailCSV', [AppointmentController::class,'downloadCSV']);
    Route::get('orderCSV', [OrderController::class,'downloadCSV']);

});

Route::get('/', [SiteController::class, 'index']);
Route::get('serviceDetail/{id}', [SiteController::class, 'show']);

Route::get('customer-login', [CustomerAuthController::class, 'index']);
Route::post('customer-post-login', [CustomerAuthController::class, 'postLogin']); 
Route::get('customer-registration', [CustomerAuthController::class, 'registration']);
Route::post('customer-post-registration', [CustomerAuthController::class, 'postRegistration']); 
Route::get('customer-logout', [CustomerAuthController::class, 'logout']);

// appointments
Route::get('booking/{id}', [ServiceAppointmentController::class, 'create']);
Route::resource('booking', ServiceAppointmentController::class);
Route::get('appointmentCSV', [ServiceAppointmentController::class,'downloadCSV']);
// Order
Route::get('checkout/{id}', 'App\Http\Controllers\Site\OrderController@checkout');
Route::get('CartCheckout', 'App\Http\Controllers\Site\OrderController@CartCheckout');
Route::resource('order', 'App\Http\Controllers\Site\OrderController');
Route::resource('transactions', 'App\Http\Controllers\Site\TransactionController');
