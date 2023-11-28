<?php

namespace App\Http\Controllers\AppController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\LongHoliday;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffGeneralHoliday;
use App\Models\StaffHoliday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller

{
    public function __construct()
    {
        $this->middleware('log.api');
    }

    public function login(Request $request)
    {
        $credentials = [
            "email" => $request->username,
            "password" => $request->password
        ];
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->username)->first();

            if ($request->has('fcmToken') && $request->fcmToken) {
                $user->device_token = $request->fcmToken;
                $user->save();
            }

            $token = $user->createToken('app-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'access_token' => $token,
            ], 200);
        }

        return response()->json(['error' => 'These credentials do not match our records.'], 401);
    }

    public function signup(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        // If validation passes, proceed with creating the user
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['email'] = strtolower(trim($input['email']));

        $user = User::create($input);
        $user->assignRole("Customer");

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ], 200);
    }

    public function index()
    {

        $slider_images = Setting::where('key', 'Slider Image')->value('value');

        $images = explode(",", $slider_images);

        $categories = ServiceCategory::get();
        $services = Service::take(10)->get();

        return response()->json([
            'images' => $images,
            'categories' => $categories,
            'services' => $services,
        ], 200);
    }

    public function availableStaff(Request $request)
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $request->date);

        $staff_ids = StaffHoliday::where('date', $request->date)->pluck('staff_id')->toArray();

        $dayName = $carbonDate->formatLocalized('%A');
        $generalHolidays = config('app.general_holiday');
        if (in_array($dayName, $generalHolidays)) {
            $holiday[] = $request->date;
        } else {
            $holiday = Holiday::where('date', $request->date)->pluck('date')->toArray();
        }
        $generalHolidayStaffIds = StaffGeneralHoliday::where('day', $dayName)->pluck('staff_id')->toArray();

        $longHolidaysStaffId = LongHoliday::where('date_start', '<=', $request->date)
            ->where('date_end', '>=', $request->date)
            ->pluck('staff_id')->toArray();
        $on_holiday_staff_ids = array_unique([...$staff_ids, ...$generalHolidayStaffIds, ...$longHolidaysStaffId]);

        if ($holiday) {
            return response()->json([
                'msg' => "Whoops! Holiday on selected date. Please choose another date by Click on Data.",
            ], 201);
        } else {
            $availableStaff = User::with('staff')->whereHas('staff', function ($query) use ($on_holiday_staff_ids) {
                $query->where('status', 1)->whereNotIn('user_id', $on_holiday_staff_ids);
            })->get();

            return response()->json([
                'availableStaff' => $availableStaff
            ], 200);
        }
    }
}
