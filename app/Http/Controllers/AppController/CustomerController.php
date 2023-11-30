<?php

namespace App\Http\Controllers\AppController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
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

    public function availableTimeSlot(Request $request)
    {
        $transport_charges = StaffZone::where('name',$request->area)->value('transport_charges');
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($request->area, $request->date);
        $availableStaff = [];
        $staff_displayed = [];
        $staff_slots = [];
        foreach ($timeSlots as $timeSlot) {
            $staff_counter = 0;
            $holiday_counter = 0;
            $booked_counter = 0;
            foreach ($timeSlot->staffs as $staff) {
                if (!in_array($staff->id, $staff_ids)) {
                    $booked_counter++;
                }
                if (!in_array($staff->id, $timeSlot->excluded_staff)) {
                    $holiday_counter++;
                }
                if (!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                    $staff_counter++;
                    $current_slot = [$timeSlot->id,  date('h:i A', strtotime($timeSlot->time_start)) . '-- ' . date('h:i A', strtotime($timeSlot->time_end)), $timeSlot->id];

                    if (isset($staff_slots[$staff->id])) {
                        array_push($staff_slots[$staff->id], $current_slot);
                    } else {
                        $staff_slots[$staff->id] = [$current_slot];
                    }
                    if (!in_array($staff->id, $staff_displayed)) {
                        $staff_displayed[] = $staff->id;
                        $availableStaff[] = $staff;
                    }
                }
            }
        }

        if (count($staff_displayed) == 0) {
            return response()->json([
                'msg' => "Whoops! No Staff Available",
            ], 201);
        }

        return response()->json([
            'transport_charges'=>$transport_charges,
            'availableStaff' => $availableStaff,
            'slots' => $staff_slots,
        ], 200);
    }

}
