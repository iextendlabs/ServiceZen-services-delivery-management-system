<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCategory;
use App\Models\AffiliateService;
use App\Models\MembershipPlan;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffHoliday;
use App\Models\StaffImages;
use App\Models\StaffDriver;
use App\Models\StaffYoutubeVideo;
use App\Models\StaffZone;
use App\Models\SubTitle;
use App\Models\TimeSlot;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:service-staff-list|service-staff-create|service-staff-edit|service-staff-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:service-staff-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:service-staff-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:service-staff-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $documents = [
        'address_proof' => 'Address Proof',
        'noc' => 'NOC',
        'id_card_front' => 'Id Card Front Side',
        'id_card_back' => 'Id Card Back Side',
        'passport' => 'Passport',
        'driving_license' => 'Driving License',
        'education' => 'Education',
        'other' => 'Other'
    ];

    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        $filter = [
            'name' => $request->name,
            'email' => $request->email,
            'sub_title' => $request->sub_title,
            'location' => $request->location,
            'min_order_value' => $request->min_order_value,
            'service_id' => $request->service_id,
            'category_id' => $request->category_id,
            'zone_id' => $request->zone_id,
            'assignedZone' => $request->assignedZone,
            'assignedTimeSlot' => $request->assignedTimeSlot,
        ];

        $sub_titles = SubTitle::all();

        $locations = Staff::where('status', 1)
            ->whereNotNull('location')
            ->pluck('location')
            ->toArray();

        $all_locations = [];
        foreach ($locations as $location) {
            $location_parts = explode('/', $location);
            $all_locations = array_merge($all_locations, array_map('trim', $location_parts));
        }
        $locations = array_unique(array_filter($all_locations));

        $services = Service::where('status', 1)->get();
        $categories = ServiceCategory::where('status', 1)->get();

        $staffZones = StaffZone::get();
        $timeSlots = TimeSlot::where('status', 1)->get();

        $query = User::orderBy($sort, $direction)
            ->whereHas('staff', function ($query) use ($request) {

                if ($request->location) {
                    $query->where('location', 'like', '%' . $request->location . '%');
                }

                if ($request->min_order_value) {
                    $query->where('min_order_value', $request->min_order_value);
                }
            })
            ->when($request->sub_title, function ($query) use ($request) {
                $query->whereHas('subTitles', function ($q) use ($request) {
                    $q->where('sub_titles.id', $request->sub_title);
                });
            });


        if ($request->service_id) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // Apply Category filter
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->zone_id) {
            $query->whereHas('staffZones', function ($q) use ($request) {
                $q->where('staff_zones.id', $request->zone_id);
            });
        }

        if (isset($request->assignedZone)) {
            if ($request->assignedZone == '1') {
                $query->whereDoesntHave('staffZones'); 
            } elseif ($request->assignedZone == '0') {
                $query->whereHas('staffZones');
            }
        }

        if (isset($request->assignedTimeSlot)) {
            if ($request->assignedTimeSlot == '1') {
                $query->whereDoesntHave('staffTimeSlots'); 
            } elseif ($request->assignedTimeSlot == '0') {
                $query->whereHas('staffTimeSlots');
            }
        }

        if (Auth::user()->hasRole('Supervisor')) {
            $staffIds = Auth::user()->getSupervisorStaffIds();
            $query->role('Staff')->whereIn('id', $staffIds);
        } elseif (Auth::user()->hasRole('Staff')) {
            $query->role('Staff')->where('id', Auth::id());
        } else {
            $query->role('Staff')->latest();
        }

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $total_staff = $query->count();
        $serviceStaff = $query->paginate(config('app.paginate'));

        $serviceStaff->appends($filter, ['sort' => $sort, 'direction' => $direction]);
        return view('serviceStaff.index', compact('total_staff', 'serviceStaff', 'filter', 'direction', 'sub_titles', 'locations', 'services', 'categories', 'staffZones', 'timeSlots'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $socialLinks = Setting::where('key', 'Social Links of Staff')->value('value');
        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $documents = $this->documents;
        $subTitles = SubTitle::all();

        $staffZones = StaffZone::get();
        $timeSlots = TimeSlot::get();
        return view('serviceStaff.create', compact('users', 'socialLinks', 'categories', 'services', 'documents', 'subTitles', 'staffZones', 'timeSlots'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'whatsapp' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'commission' => 'required',
            'id_card_front' => 'required',
            'id_card_back' => 'required',
            'passport' => 'required',
            'time_slots' => 'sometimes|array',
            'time_slots.*' => 'exists:time_slots,id',
            'zones' => 'sometimes|array',
            'zones.*' => 'exists:staff_zones,id',
        ]);

        $input = $request->all();
        $input['phone'] = $request->number_country_code . $request->phone;
        $input['whatsapp'] = $request->whatsapp_country_code . $request->whatsapp;
        $input['password'] = Hash::make($input['password']);

        $ServiceStaff = User::create($input);
        $user_id = $ServiceStaff->id;

        $input['user_id'] = $user_id;

        $ServiceStaff->assignRole('Staff');

        if ($request->gallery_images) {
            $images = $request->gallery_images;

            foreach ($images as $image) {
                $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('staff-images'), $filename);
                // dd($filename);
                StaffImages::create([
                    'image' => $filename,
                    'staff_id' => $user_id,
                ]);
            }
        }

        if ($request->image) {
            // create a unique filename for the image
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            // move the uploaded file to the public/staff-images directory
            $request->image->move(public_path('staff-images'), $filename);

            // save the filename to the gallery object and persist it to the database

            $input['image'] = $filename;
        } else {
            if (file_exists(public_path('staff-images') . '/' . "default.png")) {
                $input['image'] = "default.png";
            }
        }

        if ($request->youtube_video) {
            foreach ($request->youtube_video as $youtube_video) {
                if ($youtube_video) {
                    StaffYoutubeVideo::create([
                        'youtube_video' => $youtube_video,
                        'staff_id' => $user_id,
                    ]);
                }
            }
        }

        if (file_exists(public_path('staff-images') . '/' . "default.png")) {
            $input['image'] = "default.png";
        }

        $ServiceStaff->subTitles()->sync($request->sub_titles);

        $staff = Staff::create($input);
        $ServiceStaff->services()->attach($request->service_ids);
        $ServiceStaff->categories()->attach($request->category_ids);

        $ServiceStaff->supervisors()->attach($request->ids);
        $documents = $this->documents;
        foreach ($documents as $fileField => $dbField) {
            if ($request->hasFile($fileField)) {
                $filename = mt_rand(1000, 9999) . '.' . $request->$fileField->getClientOriginalExtension();
                $request->$fileField->move(public_path('staff-document'), $filename);
                $input[$fileField] = $filename;
            }
        }

        UserDocument::create($input);

        if ($request->categories) {
            foreach ($request->categories as $categoryData) {
                // Save Category Commission
                $affiliateCategory = AffiliateCategory::create([
                    'affiliate_id' => $user_id,
                    'category_id' => $categoryData['category_id'],
                    'commission_type' => $categoryData['commission_type'],
                    'commission' => $categoryData['category_commission'],
                ]);

                // Save Service Commissions (if any)
                if (!empty($categoryData['services'])) {
                    foreach ($categoryData['services'] as $serviceData) {
                        AffiliateService::create([
                            'affiliate_category_id' => $affiliateCategory->id,
                            'service_id' => $serviceData['service_id'],
                            'commission_type' => $serviceData['commission_type'],
                            'commission' => $serviceData['service_commission'],
                        ]);
                    }
                }
            }
        }

        $timeSlotIds = $this->handleTimeSlots($request, new HomeController());
        if (!empty($timeSlotIds)) {
            $ServiceStaff->staffTimeSlots()->sync($timeSlotIds);
        }

        $zoneIds = $this->handleZones($request, new HomeController());
        if (!empty($zoneIds)) {
            $ServiceStaff->staffZones()->sync($zoneIds);
        }

        return redirect()->route('serviceStaff.index')
            ->with('success', 'Service Staff created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $service
     * @return \Illuminate\Http\Response
     */
    public function show(User $serviceStaff, Request $request)
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $transactions = Transaction::where('user_id', $serviceStaff->id)->latest()->paginate(config('app.paginate'));

        $total_balance = Transaction::where('user_id', $serviceStaff->id)->sum('amount');

        $product_sales = Transaction::where('type', 'Product Sale')
            ->where('created_at', '>=', $currentMonth)
            ->where('user_id', $serviceStaff->id)
            ->sum('amount');

        $bonus = Transaction::where('type', 'Bonus')
            ->where('created_at', '>=', $currentMonth)
            ->where('user_id', $serviceStaff->id)
            ->sum('amount');

        $freelancer_join = $request->freelancer_join;

        $documents = $this->documents;

        $assignedDrivers = StaffDriver::where('staff_id', $serviceStaff->id)
            ->get()
            ->groupBy('day');

        $timeSlots = TimeSlot::get();

        return view('serviceStaff.show', compact('serviceStaff', 'transactions', 'total_balance', 'product_sales', 'bonus', 'freelancer_join', 'documents', 'assignedDrivers', 'timeSlots'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $serviceStaff, Request $request)
    {
        $users = User::all();
        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $socialLinks = Setting::where('key', 'Social Links of Staff')->value('value');
        $freelancer_join = $request->freelancer_join;
        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        $membership_plans = MembershipPlan::where('status', 1)
            ->where('type', "Freelancer")
            ->get();
        $documents = $this->documents;

        $assignedDrivers = StaffDriver::where('staff_id', $serviceStaff->id)
            ->get()
            ->groupBy('day');
        $staffId = $serviceStaff->id;

        $subTitles = SubTitle::all();

        $timeSlots = TimeSlot::all();
        $staffZones = StaffZone::all();
        return view('serviceStaff.edit', compact('serviceStaff', 'users', 'socialLinks', 'categories', 'services', 'freelancer_join', 'affiliates', 'membership_plans', 'documents', 'assignedDrivers', 'timeSlots', 'staffZones', 'subTitles'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ServiceStaff  $serviceStaff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'whatsapp' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'same:confirm-password',
            'commission' => 'required',
            'drivers' => 'required|array',
        ];

        if ($request->freelancer_join == 1) {
            $rules['expiry_date'] = 'required';
        }

        // Custom validation for drivers array
        Validator::make($request->all(), $rules)->after(function ($validator) use ($request) {
            $drivers = $request->input('drivers', []);

            foreach ($drivers as $day => $entries) {
                foreach ($entries as $index => $entry) {
                    $driverId = $entry['driver_id'] ?? null;
                    $timeSlotId = $entry['time_slot_id'] ?? null;

                    if (is_null($driverId) xor is_null($timeSlotId)) {
                        $validator->errors()->add("drivers.{$day}.{$index}.driver_id", "Both driver_id and time_slot_id must be provided together for {$day}.");
                        $validator->errors()->add("drivers.{$day}.{$index}.time_slot_id", "Both driver_id and time_slot_id must be provided together for {$day}.");
                    }
                }
            }
        })->validate();


        $input = $request->all();
        $input['phone'] = $request->number_country_code . $request->phone;
        $input['whatsapp'] = $request->whatsapp_country_code . $request->whatsapp;
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $serviceStaff = User::find($id);

        $staff = Staff::find($input['staff_id']);

        if ($request->gallery_images) {
            $images = $request->gallery_images;

            foreach ($images as $image) {
                $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('staff-images'), $filename);
                StaffImages::create([
                    'image' => $filename,
                    'staff_id' => $id,
                ]);
            }
        }

        if (isset($request->image)) {
            if ($staff && $staff->image && $staff->image !== "default.png"  && file_exists(public_path('staff-images') . '/' . $staff->image)) {
                unlink(public_path('staff-images') . '/' . $staff->image);
            }

            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            $request->image->move(public_path('staff-images'), $filename);

            $input['image'] = $filename;
        }

        if ($request->youtube_video) {
            StaffYoutubeVideo::where('staff_id', $id)->delete();
            foreach ($request->youtube_video as $youtube_video) {
                if ($youtube_video) {
                    StaffYoutubeVideo::create([
                        'youtube_video' => $youtube_video,
                        'staff_id' => $id,
                    ]);
                }
            }
        }

        $serviceStaff->subTitles()->sync($request->sub_titles);
        if ($staff) {
            $staff->update($input);
        } else {
            $input['user_id'] = $id;
            Staff::create($input);
        }
        $serviceStaff->supervisors()->sync($request->ids);
        $serviceStaff->services()->sync($request->service_ids);
        $serviceStaff->categories()->sync($request->category_ids);

        if ($request->freelancer_join == 1) {
            $input['freelancer_program'] = 1;
        }
        $serviceStaff->update($input);

        $serviceStaff->assignRole('Staff');


        $documentFields = $this->documents;

        $userDocuments = UserDocument::where('user_id', $id)->first();

        foreach ($documentFields as $fileField => $dbField) {
            if ($request->hasFile($fileField)) {
                $filename = mt_rand(1000, 9999) . '.' . $request->$fileField->getClientOriginalExtension();
                $request->$fileField->move(public_path('staff-document'), $filename);

                $input[$fileField] = $filename;

                if ($userDocuments && $userDocuments->$fileField) {
                    $oldFile = public_path('staff-document/' . $userDocuments->$fileField);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            } else {
                $input[$fileField] = $userDocuments->$fileField ?? null;
            }
        }

        if ($userDocuments) {
            $userDocuments->update($input);
        } else {
            $input['user_id'] = $id;
            UserDocument::create($input);
        }

        StaffDriver::where('staff_id', $id)->delete();

        foreach ($request->drivers as $day => $drivers) {
            foreach ($drivers as $driver) {
                if (!is_null($driver['driver_id']) && !is_null($driver['time_slot_id'])) {
                    StaffDriver::create([
                        'staff_id' => $id,
                        'driver_id' => $driver['driver_id'],
                        'day' => $day,
                        'time_slot_id' => $driver['time_slot_id'],
                    ]);
                }
            }
        }

        AffiliateCategory::where('affiliate_id', $id)->delete();

        if ($request->categories) {
            foreach ($request->categories as $categoryData) {
                $affiliateCategory = AffiliateCategory::create([
                    'affiliate_id' => $id,
                    'category_id' => $categoryData['category_id'],
                    'commission_type' => $categoryData['commission_type'],
                    'commission' => $categoryData['category_commission'],
                ]);

                if (!empty($categoryData['services'])) {
                    foreach ($categoryData['services'] as $serviceData) {
                        AffiliateService::create([
                            'affiliate_category_id' => $affiliateCategory->id,
                            'service_id' => $serviceData['service_id'],
                            'commission_type' => $serviceData['commission_type'],
                            'commission' => $serviceData['service_commission'],
                        ]);
                    }
                }
            }
        }
        $serviceStaff->staffTimeSlots()->sync([]);

        $timeSlotIds = $this->handleTimeSlots($request, new HomeController());
        if (!empty($timeSlotIds)) {
            $serviceStaff->staffTimeSlots()->sync($timeSlotIds);
        }

        $serviceStaff->staffZones()->sync([]);

        $zoneIds = $this->handleZones($request, new HomeController());
        if (!empty($zoneIds)) {
            $serviceStaff->staffZones()->sync($zoneIds);
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Service Staff updated successfully');
    }

    public function uploadDocument(Request $request, $id)
    {
        $documentFields = $this->documents;

        $userDocuments = UserDocument::where('user_id', $id)->first() ?? new UserDocument();

        foreach ($documentFields as $fileField => $dbField) {
            if ($request->hasFile($fileField)) {
                $filename = mt_rand(1000, 9999) . '.' . $request->$fileField->getClientOriginalExtension();
                $request->$fileField->move(public_path('staff-document'), $filename);

                if ($userDocuments->$fileField) {
                    $oldFile = public_path('staff-document/' . $userDocuments->$fileField);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $userDocuments->$fileField = $filename;
            }
        }

        $userDocuments->user_id = $id;
        $userDocuments->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceStaff  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $serviceStaff)
    {
        if (isset($serviceStaff->staff->image) && $serviceStaff->staff->image !== "default.png" && file_exists(public_path('staff-images') . '/' . $serviceStaff->staff->image)) {
            unlink(public_path('staff-images') . '/' . $serviceStaff->staff->image);
        }

        if (isset($serviceStaff->staffImages)) {

            foreach ($serviceStaff->staffImages as $image) {
                if ($image->image && file_exists(public_path('staff-images') . '/' . $image->image)) {
                    unlink(public_path('staff-images') . '/' . $image->image);
                }
            }
        }

        if (isset($serviceStaff->document)) {
            $documents = $this->documents;
            foreach ($documents as $fileField => $dbField) {
                if ($serviceStaff->document->$fileField) {
                    $oldFile = public_path('staff-document/' . $serviceStaff->document->$fileField);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }
        $serviceStaff->subTitles()->detach();
        $serviceStaff->delete();

        $previousUrl = url()->previous();

        StaffDriver::where('staff_id', $serviceStaff->id)->delete();

        return redirect($previousUrl)
            ->with('success', 'Service Staff deleted successfully');
    }

    public function removeImages(Request $request)
    {
        $staff_images = StaffImages::where('staff_id', $request->id)->pluck('image')->toArray();

        $deleteImage = $request->image;

        if (($key = array_search($deleteImage, $staff_images)) !== false) {
            if ($deleteImage && file_exists(public_path('staff-images') . '/' . $deleteImage)) {
                unlink(public_path('staff-images') . '/' . $deleteImage);
            }
        }

        StaffImages::where('image', $request->image)->delete();

        return redirect()->back()->with('success', 'Image Remove successfully.');
    }

    public function assignZones(Request $request)
    {
        $request->validate([
            'staff_ids' => 'required',
            'zones' => 'required|array',
            'zones.*' => 'exists:staff_zones,id',
        ]);

        $staffIds = explode(',', $request->staff_ids);
        $zoneIds = $request->zones;
        $replace = $request->has('replace_existing');

        foreach ($staffIds as $staffId) {
            $user = User::findOrFail($staffId);

            if ($replace) {
                // Replace existing zones - sync without detaching
                $user->staffZones()->sync($zoneIds);
            } else {
                // Add new zones without duplicates - sync without detaching
                $existingZones = $user->staffZones()->pluck('zone_id')->toArray();
                $newZones = array_diff($zoneIds, $existingZones);

                if (!empty($newZones)) {
                    $user->staffZones()->attach($newZones);
                }
            }
        }

        return redirect()->back()
            ->with('success', 'Zones assigned successfully');
    }

    public function assignTimeSlots(Request $request)
    {
        $request->validate([
            'staff_ids' => 'required',
            'time_slots' => 'required|array',
            'time_slots.*' => 'exists:time_slots,id',
        ]);

        $staffIds = explode(',', $request->staff_ids);
        $timeSlotIds = $request->time_slots;
        $replace = $request->has('replace_existing');

        foreach ($staffIds as $staffId) {
            $user = User::findOrFail($staffId);

            if ($replace) {
                // Replace existing time slots
                $user->staffTimeSlots()->sync($timeSlotIds);
            } else {
                // Add new time slots without duplicates
                $existingTimeSlots = $user->staffTimeSlots()->pluck('time_slot_id')->toArray();
                $newTimeSlots = array_diff($timeSlotIds, $existingTimeSlots);

                if (!empty($newTimeSlots)) {
                    $user->staffTimeSlots()->attach($newTimeSlots);
                }
            }
        }

        return redirect()->back()
            ->with('success', 'Time slots assigned successfully');
    }

    protected function handleTimeSlots($request, $homeController)
    {
        $timeSlotIds = [];

        if ($request->has('time_slots')) {
            $timeSlotIds = $request->time_slots;
        }

        if ($request->has('new_time_slots')) {
            foreach ($request->new_time_slots as $newTimeSlot) {
                if (empty($newTimeSlot['name']) || empty($newTimeSlot['time_start']) || empty($newTimeSlot['time_end'])) {
                    continue;
                }
                $timeId = $this->timeSlotStore($newTimeSlot);

                $timeSlotIds[] = $timeId;
            }
        }

        $homeController->appTimeSlotsData();

        return $timeSlotIds;
    }

    protected function timeSlotStore($newTimeSlot)
    {

        $exists = null;

        $exists = TimeSlot::whereNull('date')
            ->where('time_start', $newTimeSlot['time_start'])
            ->where('time_end', $newTimeSlot['time_end'])
            ->first();

        if ($exists) {
            return $exists->id;
        }

        $timeStart = Carbon::createFromFormat('H:i', $newTimeSlot['time_start']);
        $timeEnd = Carbon::createFromFormat('H:i', $newTimeSlot['time_end']);

        $carbonTimeStart = Carbon::parse($newTimeSlot['time_start']);
        $start_time_to_sec = $carbonTimeStart->hour * 3600 + $carbonTimeStart->minute * 60 + $carbonTimeStart->second;

        $carbonTimeEnd = Carbon::parse($newTimeSlot['time_end']);
        $end_time_to_sec = $carbonTimeEnd->hour * 3600 + $carbonTimeEnd->minute * 60 + $carbonTimeEnd->second;

        if ($timeStart->hour >= 12 && $timeEnd->hour < 12) {
            $end_time_to_sec = $end_time_to_sec + 86400;
        }

        $timeSlot = TimeSlot::create([
            'name' => $newTimeSlot['name'],
            'time_start' => $newTimeSlot['time_start'],
            'time_end' => $newTimeSlot['time_end'],
            'start_time_to_sec' => $start_time_to_sec,
            'end_time_to_sec' => $end_time_to_sec,
            'status' => 1,
            'type' => 'General',
            'seat' => 1
        ]);

        return $timeSlot->id;
    }

    protected function handleZones($request, $homeController)
    {
        $zoneIds = [];

        if ($request->has('zones')) {
            $zoneIds = $request->zones;
        }

        if ($request->has('new_zones')) {
            foreach ($request->new_zones as $newZone) {
                if (empty($newZone['name'])) {
                    continue;
                }
                $zone = StaffZone::where('name', $newZone['name'])->first();
                if ($zone) {
                    $zoneIds[] = $zone->id;
                } else {
                    $zone = StaffZone::create([
                        'name' => $newZone['name'],
                        'description' => $newZone['description'] ?? null,
                    ]);

                    $zoneIds[] = $zone->id;
                }
            }
        }

        $homeController->appZoneData();

        return $zoneIds;
    }
}
