<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController\{
    StaffAppController2,
    StaffAppController,
    DriverAppController,
    ChatController,
    CustomerController,
    ErrorLogController,
};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\StripePaymentController;
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
Route::get('driverNotification', [DriverAppController::class, 'notification']);
Route::get('notification', [StaffAppController2::class, 'notification']);
Route::get('driverOrders', [DriverAppController::class, 'orders']);
Route::post('driverLogin', [DriverAppController::class, 'login']);
Route::get('driverOrderStatusUpdate/{order}', [DriverAppController::class, 'orderDriverStatusUpdate']);
Route::post('updateToken', [DriverAppController::class, 'updateToken']);
Route::post('addShortHoliday', [StaffAppController2::class, 'addShortHoliday']);
Route::get('index', [StaffAppController2::class, 'index']);
Route::get('getStaffProfile', [StaffAppController2::class, 'getStaffProfile']);
Route::get('getTransactions', [StaffAppController2::class, 'getTransactions']);
Route::get('getHolidays', [StaffAppController2::class, 'getHolidays']);
Route::get('getStaffOrders', [StaffAppController2::class, 'getOrders']);
Route::get('getWithdrawPaymentMethods', [StaffAppController2::class, 'getWithdrawPaymentMethods']);
Route::post('withdraw', [StaffAppController2::class, 'withdraw']);
Route::get('getWithdraws', [StaffAppController2::class, 'getWithdraws']);
Route::post('updateProfile', [StaffAppController2::class, 'updateProfile']);
Route::post('updateUser', [StaffAppController2::class, 'updateUser']);
Route::post('onlineOffline', [StaffAppController2::class, 'onlineOffline']);
Route::get('getPlans', [StaffAppController2::class, 'getPlans']);
Route::post('staffSignup', [StaffAppController2::class, 'signup']);
Route::get('getStaffQuotes', [StaffAppController2::class, 'getQuotes']);
Route::post('/quotes/update-status', [StaffAppController2::class, 'quoteStatusUpdate']);
Route::get('/quote/{quote_id}/bid/{staff_id}', [StaffAppController2::class, 'showBidPage']);
Route::post('/bid/{quote_id}/bid/{staff_id}', [StaffAppController2::class, 'storeBid']);
Route::post('/bid/{bid_id}/update', [StaffAppController2::class, 'updateBid']);

// customer App
Route::post('customerLogin', [CustomerController::class, 'login']);
Route::post('customerSignup', [CustomerController::class, 'signup']);
Route::get('servicesTimeSlot', [CustomerController::class, 'servicesTimeSlot']);
Route::get('availableTimeSlot', [CustomerController::class, 'availableTimeSlot']);
Route::get('getOrders', [CustomerController::class, 'getOrders']);
Route::get('editOrder', [CustomerController::class, 'editOrder']);
Route::get('getServiceDetails', [CustomerController::class, 'getServiceDetails']);
Route::get('getCacheServiceDetail', [CustomerController::class, 'getCacheServiceDetail']);
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
Route::get('cancelOrder', [CustomerController::class, 'cancelOrder']);
Route::get('OrderTotalSummary', [CustomerController::class, 'OrderTotalSummary']);
Route::post('/apply-affiliate', [CustomerController::class, 'applyAffiliate'])->name('apply.affiliate');
Route::post('addNewOrder', [CustomerController::class, 'addNewOrder']);
Route::post('/create-payment-intent', [StripePaymentController::class, 'stripePost']);
Route::get('getStaff', [CustomerController::class, 'getStaff']);
Route::get('staffFilterOption', [CustomerController::class, 'staffFilterOption']);
Route::get('getServices', [CustomerController::class, 'getServices']);
Route::post('/joinFreelancerProgram', [CustomerController::class, 'joinFreelancerProgram']);
Route::get('getUser/{id}', [CustomerController::class, 'getUser'])->name('getUser');
Route::post('quoteStore', [CustomerController::class, 'quoteStore']);
Route::get('getQuotes', [CustomerController::class, 'getQuotes']);
Route::get('/quotes/{quoteId}/bids', [CustomerController::class, 'getBids']);
Route::post('/quotes/{quoteId}/confirm-bid', [CustomerController::class, 'confirmBid']);
Route::get('/bid-chat/{quoteId}/messages', [CustomerController::class, 'fetchMessages']);
Route::post('/bid-chat/{quoteId}/send', [CustomerController::class, 'sendMessage']);
Route::post('/app-log-error', [CustomerController::class, 'errorLog']);

Route::get('/resized-images/{width}x{height}/{path}', function ($width, $height, $path) {
    $parts = explode('/', $path);
    $filename = array_pop($parts);
    $folder = implode('/', $parts);
    
    // Validate inputs
    $filename = basename($filename);
    $folder = collect(explode('/', $folder))
        ->map(fn($part) => basename($part))
        ->filter()
        ->implode('/');
    
    $width = (int)$width;
    $height = (int)$height;
    
    if (!is_numeric($width) || $width <= 0 || 
        !is_numeric($height) || $height <= 0) {
        abort(400, 'Invalid parameters');
    }

    // Check for WebP support
    $acceptHeader = request()->header('Accept');
    $useWebP = str_contains($acceptHeader ?? '', 'image/webp');
    $extension = $useWebP ? 'webp' : 'jpg';
    $cacheKey = "resized_{$width}x{$height}_{$folder}_{$filename}_{$extension}";
    
    // Check cache first
    if (Cache::has($cacheKey)) {
        $cachedPath = Cache::get($cacheKey);
        $headers = [
            'Content-Type' => "image/{$extension}",
            'Cache-Control' => 'public, max-age=31536000',
            'Vary' => 'Accept'
        ];
        return response()->file($cachedPath, $headers);
    }

    $originalPath = public_path("{$folder}/{$filename}");
    
    if (!file_exists($originalPath)) {
        abort(404);
    }

    $image = Image::make($originalPath);
    $originalRatio = $image->width() / $image->height();
    $targetRatio = $width / $height;

    // Resize strategy
    if ($originalRatio > $targetRatio) {
        $image->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    } else {
        $image->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    // Encode image
    $quality = $useWebP ? 80 : 85;
    $encodedImage = $image->encode($extension, $quality);

    // Store in cache
    $cacheDir = storage_path('app/cache');
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $tempPath = "{$cacheDir}/{$cacheKey}.{$extension}";
    $encodedImage->save($tempPath);
    Cache::put($cacheKey, $tempPath, now()->addDays(30));

    return response($encodedImage, 200)
        ->header('Content-Type', "image/{$extension}")
        ->header('Cache-Control', 'public, max-age=31536000')
        ->header('Vary', 'Accept');
})->where('path', '.*');

Route::post('/log-error', [ErrorLogController::class, 'store']);