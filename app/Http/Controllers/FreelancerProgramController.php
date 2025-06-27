<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Chat;
use App\Models\Staff;
use App\Models\StaffImages;
use App\Models\StaffYoutubeVideo;
use App\Models\TimeSlotToStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FreelancerProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $filter_status = $request->status;
        $filter_name = $request->name;
        $filter_email = $request->email;

        $query = User::whereNotNull('freelancer_program');
        
        if ($request->has('status')) {
            switch ($request->status) {
                case '1': // Accepted
                    $query->where('freelancer_program', 1);
                    break;
                    
                case '0': // Rejected without staff
                    $query->where('freelancer_program', 0)->whereDoesntHave('staff');
                    break;
                    
                case '2': // Rejected with staff
                    $query->where('freelancer_program', 0)->has('staff');
                    break;
            }
        }

        if (isset($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if (isset($request->email)) {
            $query->where('email', $request->email);
        }

        $users = $query->paginate(config('app.paginate'));

        $filters = $request->only(['status']);
        $users->appends($filters);
        return view('freelancerProgram.index', compact('users', 'filter_status', 'filter_name', 'filter_email'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 

    }

    public function show(User $user)
    {
        //    
    }


    public function edit($id, Request $request)
    {
        $user = User::find($id);
        if ($request->status == "Accepted") {
            $url = route('serviceStaff.edit', $id) . '?freelancer_join=1';

            return redirect($url);
        } elseif ($request->status == "Rejected") {
            $staff = Staff::where('user_id', $id)->first();
            if (isset($staff->image) && $staff->image !== "default.png" && file_exists(public_path('staff-images') . '/' . $staff->image)) {
                unlink(public_path('staff-images') . '/' . $staff->image);
            }

            if (isset($user->staffImages)) {

                foreach ($user->staffImages as $image) {
                    if ($image->image && file_exists(public_path('staff-images') . '/' . $image->image)) {
                        unlink(public_path('staff-images') . '/' . $image->image);
                    }
                }
            }

            StaffImages::where('staff_id', $id)->delete();
            StaffYoutubeVideo::where('staff_id', $id)->delete();

            $user->services()->detach();
            $user->categories()->detach();
            $user->staffTimeSlots()->detach();
            $user->staffZones()->detach();

            $staff->delete();
            TimeSlotToStaff::where('staff_id', $id)->delete();
            $user->freelancer_program = 0;
            $user->update();
            $user->removeRole("Staff");
            return redirect()->route('freelancerProgram.index')->with('success', 'New Freelancer Joinee Rejected.');
        }
    }


    public function update(Request $request, $id)
    {
        //    
    }


    public function destroy($id)
    {
        $user = User::find($id);

        $staff = Staff::where('user_id', $id)->first();
        if (isset($staff->image) && $staff->image !== "default.png" && file_exists(public_path('staff-images') . '/' . $staff->image)) {
            unlink(public_path('staff-images') . '/' . $staff->image);
        }

        if (isset($user->staffImages)) {

            foreach ($user->staffImages as $image) {
                if ($image->image && file_exists(public_path('staff-images') . '/' . $image->image)) {
                    unlink(public_path('staff-images') . '/' . $image->image);
                }
            }
        }

        StaffImages::where('staff_id', $id)->delete();
        StaffYoutubeVideo::where('staff_id', $id)->delete();

        $user->services()->detach();
        $user->categories()->detach();
        $user->staffTimeSlots()->detach();
        $user->staffZones()->detach();

        $staff && $staff->delete();
        TimeSlotToStaff::where('staff_id', $id)->delete();
        $user->freelancer_program = null;
        $user->update();
        $user->removeRole("Staff");
        return redirect()->route('freelancerProgram.index')->with('success', 'Freelancer Joinee Deleted Successfully.');
    }
}
