<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    MembershipPlanController,
    AffiliateProgramController,
    StaffGeneralHolidayController,
    BackupController,
    BidChatController,
    BidController,
    CouponController,
    FAQController,
    LongHolidayController,
    ReviewController,
    SettingController,
    ShortHolidayController,
    RotaController,
    ChatController,
    CampaignController,
    SummerNoteController,
    ComplaintController,
    CRMController,
    CurrencyController,
    FreelancerProgramController,
    InformationController,
    KommoController,
    QuoteController,
    StripePaymentController,
    WithdrawController
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
    InformationPageController,
    SiteBidController,
    SiteComplaintController,
    SiteInformationController,
    SiteQuoteController,
};
use Illuminate\Support\Facades\Cache;

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
    Route::post('/customers/bulkAssignCoupon', [CustomerController::class, 'bulkAssignCoupon'])->name('customers.bulkAssignCoupon');

    Route::get('orderCSV', [OrderController::class, 'downloadCSV']);

    Route::get('holidays', [HolidayController::class, 'index']);
    Route::post('/holidays/crud-ajax', [HolidayController::class, 'store']);
    Route::get('time-slots', [TimeSlotController::class, 'slots']);
    Route::get('staff-by-group', [TimeSlotController::class, 'staff_group']);

    Route::resource('cashCollection', CashCollectionController::class);
    Route::get('staffCashCollection', [CashCollectionController::class, 'staffCashCollection'])->name('staffCashCollection');
    Route::get('cashCollection/create/{order}', [CashCollectionController::class, 'create'])->name('cashCollection.create');
    Route::get('cashCollectionUpdate/{id}', [CashCollectionController::class, 'cashCollectionUpdate'])->name('cashCollectionUpdate');

    Route::get('profile/{id}', [HomeController::class, 'profile'])->name('profile');
    Route::post('updateProfile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfile');

    Route::resource('coupons', CouponController::class);
    Route::resource('FAQs', FAQController::class);
    Route::resource('information', InformationController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('reviews', ReviewController::class);
    Route::get('/removeReviewImages', [ReviewController::class, 'removeImages']);
    Route::get('/removeReviewVideo', [ReviewController::class, 'removeVideo']);

    Route::get('/removeStaffImages', [ServiceStaffController::class, 'removeImages']);
    Route::post('/serviceStaff/{id}/upload-document', [ServiceStaffController::class, 'uploadDocument'])->name('serviceStaff.upload.document');

    Route::get('/transactionUnapprove', [TransactionController::class,'Unapprove'])->name('transactions.Unapprove');

    Route::get('orderChat/{id}', [OrderController::class, 'orderChat'])->name('orders.chat');
    Route::post('chatUpdate/{id}', [OrderController::class, 'chatUpdate'])->name('orders.chatUpdate');
    Route::post('/customers/{customerId}/assign-coupon', [CustomerController::class, 'assignCoupon'])->name('coupons.assign');
    Route::post('/customers/{couponId}/destroy', [CustomerController::class, 'customerCoupon_destroy'])->name('customerCoupon.destroy');
    Route::get('removeCoupon/{id}', [OrderController::class, 'removeCoupon'])->name('orders.removeCoupon');
    Route::post('addDiscount/{id}', [OrderController::class, 'addDiscount'])->name('orders.addDiscount');
    Route::get('order-update/{order}', [OrderController::class, 'updateOrderStatus'])->name('updateOrderStatus');

    // affiliate export
    Route::get('/affiliate/exportTransaction/{User}', [AffiliateController::class, 'exportTransaction']);

    Route::get('/rota', [RotaController::class, 'index'])->name('rota');
    Route::resource('chats', ChatController::class);
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::resource('campaigns', CampaignController::class);
    Route::get('clear', [CampaignController::class,"clear"])->name('campaigns.clear');

    Route::post('/summerNote/upload', [SummerNoteController::class,"upload"])->name('summerNote.upload');
    Route::get('appData', [HomeController::class,"appJsonData"])->name('appData');

    Route::get('/log', [OrderController::class, 'showLog'])->name('log.show');
    Route::post('/log/empty', [OrderController::class, 'emptyLog'])->name('log.empty');


    Route::post('/affiliate_edit/{id}', [OrderController::class, 'affiliate_edit'])->name('orders.affiliate_edit');
    Route::post('/booking_edit/{id}', [OrderController::class, 'booking_edit'])->name('orders.booking_edit');
    Route::post('/custom_location/{id}', [OrderController::class, 'custom_location'])->name('orders.custom_location');
    Route::post('/detail_edit/{id}', [OrderController::class, 'detail_edit'])->name('orders.detail_edit');
    Route::post('/comment_edit/{id}', [OrderController::class, 'comment_edit'])->name('orders.comment_edit');
    Route::post('/driver_edit/{id}', [OrderController::class, 'driver_edit'])->name('orders.driver_edit');
    Route::post('/driver_status_edit/{id}', [OrderController::class, 'driver_status_edit'])->name('orders.driver_status_edit');
    Route::post('/status_edit/{id}', [OrderController::class, 'status_edit'])->name('orders.status_edit');
    Route::post('/services_edit/{id}', [OrderController::class, 'services_edit'])->name('orders.services_edit');
    Route::post('/orders/bulkStatusEdit', [OrderController::class, 'bulkStatusEdit'])->name('orders.bulkStatusEdit');

    Route::resource('affiliateProgram', AffiliateProgramController::class);
    Route::resource('freelancerProgram', FreelancerProgramController::class);
    Route::resource('complaints', ComplaintController::class);
    Route::post('/add-complaint-chat', [ComplaintController::class, 'addComplaintChat'])->name('complaints.addComplaintChat');
    Route::get('/removeSliderImage', [SettingController::class, 'removeSliderImage']);

    Route::post('/customers/update-affiliate', [CustomerController::class, 'bulkUpdateAffiliate'])->name('customers.updateAffiliate');

    Route::resource('withdraws', WithdrawController::class);
    Route::get('withdraws-update/{withdraw}', [WithdrawController::class, 'updateWithdrawStatus'])->name('updateWithdrawStatus'); 
    Route::post('/apply-order-coupon', [OrderController::class,'applyOrderCoupon'])->name('apply.order_coupon');
    Route::get('/staff-categories-services', [OrderController::class,'staffCategoriesServices'])->name('fetch.staff_categories_services');
    Route::post('/bulkOrderBooking', [OrderController::class,'bulkOrderBooking'])->name('bulkOrderBooking');
    
    Route::resource('currencies', CurrencyController::class);

    Route::resource('membershipPlans', MembershipPlanController::class);
    Route::get('load-customers', [CouponController::class, 'loadCustomers'])->name('customers.load');

    Route::resource('quotes', QuoteController::class);
    Route::post('/quotes/bulkStatusEdit', [QuoteController::class, 'bulkStatusEdit'])->name('quotes.bulkStatusEdit');
    Route::post('/quotes/bulkAssignStaff', [QuoteController::class, 'bulkAssignStaff'])->name('quotes.bulkAssignStaff');
    Route::delete('/quotes/{quote}/staff/{staff}', [QuoteController::class, 'detachStaff'])->name('quotes.detachStaff');
    Route::post('/quotes/update-status', [QuoteController::class, 'updateStatus'])->name('quotes.updateStatus');
    Route::get('/quote/{quote_id}/bid/{staff_id}', [BidController::class, 'showBidPage'])->name('quote.bid');
    Route::post('/quote/{quote_id}/bid/{staff_id}', [BidController::class, 'store'])->name('quote.bid.store');
    Route::post('/bid/{bid_id}/update', [BidController::class, 'updateBid'])->name('bid.update');
    Route::get('/bid-chat/{bid_id}/messages', [BidChatController::class, 'fetchMessages'])->name('bid.chat.fetch');
    Route::post('/bid-chat/{bid_id}/send', [BidChatController::class, 'sendMessage'])->name('bid.chat.send');
    Route::get('/quote/{quote_id}/bids', [BidController::class, 'index'])->name('quote.bids');
    Route::post('/quotes/update-staff-data', [QuoteController::class, 'updateStaffData'])->name('quotes.updateStaffData');

    Route::get('/get-services-by-category', [AffiliateController::class, 'getServicesByCategory'])->name('getServicesByCategory');

    Route::resource('crms', CRMController::class);

    Route::get('/clear-cache', function () {
        Cache::flush(); // Clears all cache
        return redirect()->back()->with('success', 'Cache cleared!');
    })->name('cache.clear');

});
Route::get('/stripe-staff-form', [StripePaymentController::class, 'stripeStaffForm'])->name('stripe.staff.form');

Route::get('/service-category-list', [ServiceCategoryController::class, 'listServiceCategory'])->name('service-category-list');

Route::get('customer-login', [CustomerAuthController::class, 'index'])->name('customer.login');
Route::post('customer-post-login', [CustomerAuthController::class, 'postLogin'])->name('customer.post-login');
Route::get('customer-registration', [CustomerAuthController::class, 'registration'])->name('customer.registration');
Route::post('customer-post-registration', [CustomerAuthController::class, 'postRegistration'])->name('customer.post-registration');

Route::group(['middleware' => 'checkSessionExpiry'], function () {
    Route::get('customer-logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::resource('customerProfile', CustomerAuthController::class);
    Route::resource('order', SiteOrdersController::class);
    Route::get('reOrder/{id}', [SiteOrdersController::class, 'reOrder'])->name('order.reOrder');
    Route::resource('affiliate_dashboard', AffiliateDashboardController::class);
    Route::get('staffOrderCSV', [SiteOrdersController::class, 'downloadCSV']);
    Route::resource('siteComplaints', SiteComplaintController::class);
    Route::post('affiliateWithdraw', [AffiliateDashboardController::class, 'affiliateWithdraw'])->name('affiliate.withdraw');
    Route::post('internalTransfer', [AffiliateDashboardController::class, 'internalTransfer'])->name('affiliate.transfer');
    Route::post('deposit', [AffiliateDashboardController::class, 'deposit'])->name('affiliate.deposit');

    Route::get('/siteQuote/{quote_id}/bids', [SiteBidController::class, 'index'])->name('site.quote.bids');
    Route::get('/siteQuote/{quote_id}/bid/{staff_id}', [SiteBidController::class, 'showBidPage'])->name('site.quote.bid');

    Route::post('/siteQuote/update-status', [SiteQuoteController::class, 'updateStatus'])->name('siteQuote.updateStatus');
});

Route::resource('siteQuotes', SiteQuoteController::class);

Route::get('/quoteModal/{serviceId}', [SiteQuoteController::class,'quoteModal'])->name('quoteModal');
// Backups
Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
Route::get('/backups/backup', [BackupController::class, 'backup'])->name('backups.backup');
Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
Route::get('/backups/delete/{filename}', [BackupController::class, 'delete'])->name('backups.delete');
Route::get('/backups/clear', [BackupController::class, 'clear'])->name('backups.clear');

Route::get('/', [SiteController::class, 'index'])->name('storeHome');
Route::get('/service-list', [SiteController::class, 'service_list']);
Route::get('serviceDetail/{id}', [SiteController::class, 'show']);
Route::get('updateZone', [SiteController::class, 'updateZone']);


Route::get('deleteAccount', [CustomerAuthController::class, 'account']);
Route::post('deleteAccountMail', [CustomerAuthController::class, 'deleteAccountMail'])->name('deleteAccountMail');
Route::get('deleteAccountPage', [CustomerAuthController::class, 'deleteAccount'])->name('deleteAccountPage');

Route::resource('staffProfile', StaffProfileController::class);
Route::get('removeToCart/{id}', [CheckOutController::class, 'removeToCart']);
Route::post('draftOrder', [CheckOutController::class, 'draftOrder']);
Route::resource('cart', CheckOutController::class);
Route::get('bookingStep', [CheckOutController::class, 'bookingStep']);
Route::post('confirmStep', [CheckOutController::class, 'confirmStep'])->name('confirmStep');
//TODO :set no cache headers for all ajax calls
Route::middleware('no-cache')->get('slots', [CheckOutController::class, 'slots']);
Route::get('staff-group', [CheckOutController::class, 'staff_group']);
Route::post('saveLocation', [SiteController::class, 'saveLocation']);
Route::resource('siteFAQs', SiteFAQsController::class);
Route::get('applyCoupon', [CustomerAuthController::class, 'applyCoupon']);
Route::get('cancelOrder/{id}', [SiteOrdersController::class, 'cancelOrder'])->name('cancelOrder');
Route::resource('siteInformationPage', SiteInformationController::class);

//TODO :Customer Delete
// app url

// Staff app

// TODO: save and continue buttons , save and close buttons 
Route::get('/termsCondition', [InformationPageController::class, 'index'])->name('TermsCondition');
Route::get('/aboutUs', [InformationPageController::class, 'aboutUs'])->name('aboutUs');
Route::get('/privacyPolicy', [InformationPageController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('/contactUs', [InformationPageController::class, 'contactUs'])->name('contactUs');

Route::resource('siteReviews', SiteReviewsController::class);
Route::get('/category', function () {
    return view('site.categories.index');
})->name('categories.index');
Route::get('category/{id}', [SiteController::class, 'categoryShow'])->name('category.show');

Route::get('/af', [CustomerAuthController::class, 'affiliateUrl'])->name('affiliateUrl');

Route::post('/apply-coupon', [CheckOutController::class,'applyCoupon'])->name('apply.coupon');
Route::post('/apply-affiliate', [CustomerAuthController::class,'applyAffiliate'])->name('apply.affiliate');
Route::get('/join-affiliate-program', [CustomerAuthController::class,'JoinAffiliateProgram'])->name('apply.affiliateProgram');
Route::get('/addToCartModal/{serviceId}', [CheckOutController::class,'addToCartModal'])->name('addToCartModal');
Route::post('/addToCartServicesStaff', [CheckOutController::class,'addToCartServicesStaff'])->name('addToCartServicesStaff');
Route::get('/checkBooking', [CheckOutController::class,'checkBooking'])->name('checkBooking');
Route::post('/format-currency', [CheckOutController::class, 'formatCurrencyJS'])->name('format-currency');
Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe')->name('stripe.form');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});
Route::get('/checkout-success', function () {
    return view('site.checkOut.success');
})->name('checkout.success');

Route::post('/kommo-store', [KommoController::class, 'store']);

// Update Route (POST)
Route::post('/kommo-update', [KommoController::class, 'update']);

Route::post('/kommo-incomingLead', [KommoController::class, 'incomingLead']);

Route::post('/kommo-log', function (Request $request) {
    $requestData = $request->all();

    // Encode the request data as a JSON string
    $jsonData = json_encode($requestData, JSON_PRETTY_PRINT);

    // Log the JSON data
    Log::channel('kommo_log')->info('Request Received:', ['data' => $jsonData]);
    
    return response()->json(['status' => 'logged']);
});