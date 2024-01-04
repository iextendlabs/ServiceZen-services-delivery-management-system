<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    HomeController,
    RoleController,
    UserController,
    ServiceController,
    ServiceStaffController,
    CustomerController,
    AssistantSupervisorController,
    CashCollectionController,
    HolidayController,
    ManagerController,
    ServiceCategoryController,
    OrderController,
    StaffGroupController,
    StaffHolidayController,
    StaffZoneController,
    SupervisorController,
    TimeSlotController,
    TransactionController,
    DriverController,
    AffiliateController,
    StaffGeneralHolidayController,
    BackupController,
    CouponController,
    FAQController,
    LongHolidayController,
    ReviewController,
    SettingController,
    ShortHolidayController,
    RotaController,
    ChatController
};

use App\Http\Controllers\AppController\{
    StaffAppController,
    DriverAppController
};

use App\Http\Controllers\Site\{
    CheckOutController,
    CustomerAuthController,
    SiteController,
    SiteOrdersController,
    AffiliateDashboardController,
    SiteFAQsController,
    SiteReviewsController,
    StaffProfileController,
    TermsCondition,
};

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

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('serviceStaff', ServiceStaffController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('serviceCategories', ServiceCategoryController::class);
    Route::resource('affiliates', AffiliateController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('staffZones', StaffZoneController::class);
    Route::resource('staffGroups', StaffGroupController::class);
    Route::resource('managers', ManagerController::class);
    Route::resource('supervisors', SupervisorController::class);
    Route::resource('timeSlots', TimeSlotController::class);
    Route::resource('assistantSupervisors', AssistantSupervisorController::class);
    Route::resource('staffHolidays', StaffHolidayController::class);
    Route::resource('shortHolidays', ShortHolidayController::class);
    Route::resource('longHolidays', LongHolidayController::class);
    Route::resource('staffGeneralHolidays', StaffGeneralHolidayController::class);
    Route::post('/shortHolidayBulkDelete', [ShortHolidayController::class, 'bulkDelete'])->name('shortHolidays.bulkDelete');
    Route::post('/longHolidayBulkDelete', [longHolidayController::class, 'bulkDelete'])->name('longHolidays.bulkDelete');
    Route::post('/serviceBulkDelete', [ServiceController::class, 'bulkDelete'])->name('services.bulkDelete');
    Route::post('/services/bulkCopy', [ServiceController::class, 'bulkCopy'])->name('services.bulkCopy');
    Route::post('/services/bulkEdit', [ServiceController::class, 'bulkEdit'])->name('services.bulkEdit');
    Route::get('/serviceDelete/{id}', [ServiceController::class, 'destroy'])->name('service.delete');
    Route::get('shortHolidays-update/{shortHoliday}', [ShortHolidayController::class, 'updateStatus'])->name('updateStatus');

    Route::get('orderCSV', [OrderController::class, 'downloadCSV']);

    Route::get('holidays', [HolidayController::class, 'index']);
    Route::post('/holidays/crud-ajax', [HolidayController::class, 'store']);
    Route::get('time-slots', [TimeSlotController::class, 'slots']);
    Route::get('staff-by-group', [TimeSlotController::class, 'staff_group']);

    Route::resource('cashCollection', CashCollectionController::class);
    Route::get('staffCashCollection', [CashCollectionController::class, 'staffCashCollection'])->name('staffCashCollection');
    Route::get('cashCollection/create/{order}', [CashCollectionController::class, 'create'])->name('cashCollection.create');

    Route::get('profile/{id}', [HomeController::class, 'profile'])->name('profile');
    Route::post('updateProfile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfile');

    Route::resource('coupons', CouponController::class);
    Route::resource('FAQs', FAQController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('reviews', ReviewController::class);
    Route::get('/removeReviewImages', [ReviewController::class, 'removeImages']);
    Route::get('/removeReviewVideo', [ReviewController::class, 'removeVideo']);

    Route::get('/removeStaffImages', [ServiceStaffController::class, 'removeImages']);

    Route::get('/transactionUnapprove', [TransactionController::class,'Unapprove'])->name('transactions.Unapprove');

    Route::get('orderChat/{id}', [OrderController::class, 'orderChat'])->name('orders.chat');
    Route::post('chatUpdate/{id}', [OrderController::class, 'chatUpdate'])->name('orders.chatUpdate');
    Route::post('/customers/{customerId}/assign-coupon', [CustomerController::class, 'assignCoupon'])->name('coupons.assign');
    Route::post('/customers/{couponId}/destroy', [CustomerController::class, 'customerCoupon_destroy'])->name('customerCoupon.destroy');

    // affiliate export
    Route::get('/affiliate/exportTransaction/{User}', [AffiliateController::class, 'exportTransaction']);

    Route::get('/rota', [RotaController::class, 'index'])->name('rota');
    Route::resource('chats', ChatController::class);
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');

});

// Backups
Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
Route::get('/backups/backup', [BackupController::class, 'backup'])->name('backups.backup');
Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
Route::get('/backups/delete/{filename}', [BackupController::class, 'delete'])->name('backups.delete');
Route::get('/backups/clear', [BackupController::class, 'clear'])->name('backups.clear');

Route::get('/', [SiteController::class, 'index'])->name('storeHome');
Route::get('serviceDetail/{id}', [SiteController::class, 'show']);
Route::get('updateZone', [SiteController::class, 'updateZone']);

Route::get('customer-login', [CustomerAuthController::class, 'index']);
Route::post('customer-post-login', [CustomerAuthController::class, 'postLogin']);
Route::get('customer-registration', [CustomerAuthController::class, 'registration']);
Route::post('customer-post-registration', [CustomerAuthController::class, 'postRegistration']);
Route::get('customer-logout', [CustomerAuthController::class, 'logout']);
Route::resource('customerProfile', CustomerAuthController::class);


Route::resource('order', SiteOrdersController::class);
Route::get('order-update/{order}', [OrderController::class, 'updateOrderStatus'])->name('updateOrderStatus');
Route::get('reOrder/{id}', [SiteOrdersController::class, 'reOrder'])->name('order.reOrder');
Route::get('cashCollectionUpdate/{id}', [CashCollectionController::class, 'cashCollectionUpdate'])->name('cashCollectionUpdate');
Route::resource('affiliate_dashboard', AffiliateDashboardController::class);
Route::get('manageAppointment', 'App\Http\Controllers\Site\ManagerController@appointment');
Route::get('supervisor', 'App\Http\Controllers\Site\ManagerController@supervisor');
Route::get('addToCart/{id}', [CheckOutController::class, 'addToCart']);
Route::get('removeToCart/{id}', [CheckOutController::class, 'removeToCart']);
Route::post('storeSession', [CheckOutController::class, 'storeSession']);
Route::resource('cart', CheckOutController::class);
Route::get('bookingStep', [CheckOutController::class, 'bookingStep']);
Route::get('confirmStep', [CheckOutController::class, 'confirmStep']);
//TODO :set no cache headers for all ajax calls
Route::middleware('no-cache')->get('slots', [CheckOutController::class, 'slots']);
Route::get('staff-group', [CheckOutController::class, 'staff_group']);
Route::get('staffOrderCSV', [SiteOrdersController::class, 'downloadCSV']);
Route::post('saveLocation', [SiteController::class, 'saveLocation']);
Route::resource('siteFAQs', SiteFAQsController::class);
Route::get('applyCoupon', [CustomerAuthController::class, 'applyCoupon']);

//TODO :Customer Delete
// app url

// Staff app

// TODO: save and continue buttons , save and close buttons 
Route::get('/termsCondition', [TermsCondition::class, 'index'])->name('TermsCondition');

Route::get('staffAppOrders', [StaffAppController::class, 'orders']);
Route::get('staffAppUser', [StaffAppController::class, 'user']);
Route::get('staffAppAddOrderComment/{order}', [StaffAppController::class, 'addComment']);
Route::get('staffAppCashCollection/{order}', [StaffAppController::class, 'cashCollection']);
Route::get('staffAppOrderStatusUpdate/{order}', [StaffAppController::class, 'orderStatusUpdate']);
Route::get('staffAppRescheduleOrder/{order}', [StaffAppController::class, 'rescheduleOrder']);
Route::get('staffAppTimeSlots', [StaffAppController::class, 'timeSlots']);
Route::resource('staffProfile', StaffProfileController::class);
Route::get('/removeSliderImage', [SettingController::class, 'removeSliderImage']);

Route::resource('siteReviews', SiteReviewsController::class);
Route::get('/category', function () {
    return view('site.categories.index');
})->name('categories.index');

Route::get('/af', [CustomerAuthController::class, 'affiliateUrl'])->name('affiliateUrl');