<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffHoliday;
use App\Models\StaffImages;
use App\Models\StaffYoutubeVideo;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

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
    public function index(Request $request)
    {
        $filter_name = $request->name;

        if (Auth::user()->hasRole('Supervisor')) {
            $staffIds = Auth::user()->getSupervisorStaffIds();
            $query = User::role('Staff')->whereIn('id', $staffIds);
        } elseif (Auth::user()->hasRole('Staff')) {
            $query = User::role('Staff')->where('id', Auth::id());
        } else {
            $query = User::role('Staff')->latest();
        }

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }

        $serviceStaff = $query->paginate(config('app.paginate'));

        return view('serviceStaff.index', compact('serviceStaff', 'filter_name'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
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
        return view('serviceStaff.create', compact('users', 'socialLinks'));
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
            'email' => 'required|email|unique:users,email',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|same:confirm-password',
            'commission' => 'required',
        ]);

        $input = $request->all();

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

        Staff::create($input);
        $ServiceStaff->supervisors()->attach($request->ids);
        return redirect()->route('serviceStaff.index')
            ->with('success', 'Service Staff created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $service
     * @return \Illuminate\Http\Response
     */
    public function show(User $serviceStaff)
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

        return view('serviceStaff.show', compact('serviceStaff', 'transactions', 'total_balance', 'product_sales', 'bonus'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $serviceStaff)
    {
        $users = User::all();
        $socialLinks = Setting::where('key', 'Social Links of Staff')->value('value');
        $supervisor_ids = $serviceStaff->supervisors()->pluck('supervisor_id')->toArray();

        return view('serviceStaff.edit', compact('serviceStaff', 'users', 'socialLinks', 'supervisor_ids'));
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
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'same:confirm-password',
            'commission' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $serviceStaff = User::find($id);
        $serviceStaff->update($input);

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
            //delete previous Image if new Image submitted
            if ($staff->image && file_exists(public_path('staff-images') . '/' . $staff->image)) {
                unlink(public_path('staff-images') . '/' . $staff->image);
            }

            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            // move the uploaded file to the public/staff-images directory
            $request->image->move(public_path('staff-images'), $filename);

            // save the filename to the gallery object and persist it to the database

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

        $staff->update($input);
        $serviceStaff->supervisors()->sync($request->ids);

        return redirect()->route('serviceStaff.index')
            ->with('success', 'Service Staff updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceStaff  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $serviceStaff)
    {
        if (isset($serviceStaff->staff->image) && file_exists(public_path('staff-images') . '/' . $serviceStaff->staff->image)) {
            unlink(public_path('staff-images') . '/' . $serviceStaff->staff->image);
        }

        if (isset($serviceStaff->staffImages)) {

            foreach ($serviceStaff->staffImages as $image) {
                if ($image->image && file_exists(public_path('staff-images') . '/' . $image->image)) {
                    unlink(public_path('staff-images') . '/' . $image->image);
                }
            }
        }

        $serviceStaff->delete();

        return redirect()->route('serviceStaff.index')
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

        StaffImages::where('image',$request->image)->delete();

        return redirect()->back()->with('success', 'Image Remove successfully.');
    }
}
