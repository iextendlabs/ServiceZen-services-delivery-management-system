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
use App\Http\Controllers\AssistantSupervisorController;
use App\Http\Controllers\CashCollectionController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffGroupController;
use App\Http\Controllers\StaffZoneController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\TransactionController;

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
    Route::resource('staffGroups', StaffGroupController::class);
    Route::resource('managers', ManagerController::class);
    Route::resource('supervisors', SupervisorController::class);
    Route::resource('timeSlots', TimeSlotController::class);
    Route::resource('cashCollection', CashCollectionController::class);
    Route::resource('assistantSupervisors', AssistantSupervisorController::class);

    Route::post('serviceFilter', [ServiceController::class, 'filter']);
    Route::post('appointmentFilter', [AppointmentController::class, 'filter']); 
    Route::post('orderFilter', [OrderController::class, 'filter']); 
    Route::post('serviceStaffFilter', [ServiceStaffController::class, 'filter']); 
    Route::post('customerFilter', [CustomerController::class, 'filter']); 
    Route::post('affiliateFilter', [AffiliateController::class, 'filter']); 
    Route::post('userFilter', [UserController::class, 'filter']); 
    Route::post('managerFilter', [ManagerController::class, 'filter']); 
    Route::post('supervisorFilter', [SupervisorController::class, 'filter']);
    Route::post('assistantSupervisorFilter', [AssistantSupervisorController::class, 'filter']);
     
    Route::get('serviceFilterCategory', [ServiceController::class, 'filter']); 

    Route::get('appointmentDetailCSV', [AppointmentController::class,'downloadCSV']);
    Route::get('appointmentPrint', [AppointmentController::class,'print']);
    Route::get('orderCSV', [OrderController::class,'downloadCSV']);

    Route::get('holidays', [HolidayController::class, 'index']);
    Route::post('/holidays/crud-ajax', [HolidayController::class, 'store']);
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
Route::get('slots', [ServiceAppointmentController::class,'slots']);
Route::get('staff-group', [ServiceAppointmentController::class,'staff_group']);
// Order
Route::get('checkout/{id}', 'App\Http\Controllers\Site\OrderController@checkout');
Route::get('CartCheckout', 'App\Http\Controllers\Site\OrderController@CartCheckout');
Route::resource('order', 'App\Http\Controllers\Site\OrderController');
Route::resource('transactions', 'App\Http\Controllers\Site\TransactionController');
Route::get('manageAppointment', 'App\Http\Controllers\Site\ManagerController@appointment');
Route::get('supervisor', 'App\Http\Controllers\Site\ManagerController@supervisor');
Route::resource('cashCollections', 'App\Http\Controllers\Site\CashCollectionController');
