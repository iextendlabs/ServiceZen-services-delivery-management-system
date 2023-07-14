<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Service;
use App\Models\Staff;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Console\Command\DumpCompletionCommand;
use Illuminate\Support\Facades\Session;

class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Session::get('serviceIds');
        if (Session::get('address') && Session::get('serviceIds')) {
            return redirect('step3');
        } else {
            if ($services) {
                foreach ($services as $service) {
                    $booked_services[] = Service::find($service);
                }
            } else {
                $booked_services = array();
            }

            return view('site.checkOut.index', compact('booked_services'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
        }
    }

    public function addToCart(Request $request, $id)
    {

        $serviceId = $id;
        $serviceIds = Session::get('serviceIds', []);

        if (!in_array($serviceId, $serviceIds)) {
            $serviceIds[] = $serviceId;
            Session::put('serviceIds', $serviceIds);
        }

        return redirect()->back()->with('cart-success', 'Service Add to Cart Successfully.');
    }

    public function removeToCart(Request $request, $id)
    {

        $idToRemove = $id;

        $serviceIds = Session::get('serviceIds', []);

        $index = array_search($idToRemove, $serviceIds);

        if ($index !== false) {
            array_splice($serviceIds, $index, 1);
        }

        Session::put('serviceIds', $serviceIds);

        return redirect()->back()->with('success', 'Service Remove to Cart Successfully.');
    }

    public function addressSession(Request $request)
    {
        $this->validate($request, [
            'buildingName' => 'required',
            'area' => 'required',
            'flatVilla' => 'required',
            'street' => 'required',
            'landmark' => 'required',
            'name' => 'required',
            'number' => 'required',
            'email' => 'required|email',
            'name' => 'required',
            'whatsapp' => 'required',
        ]);

        $address = [];

        $address['buildingName'] = $request->buildingName;
        $address['area'] = $request->area;
        $address['flatVilla'] = $request->flatVilla;
        $address['street'] = $request->street;
        $address['landmark'] = $request->landmark;
        $address['city'] = $request->city;
        $address['number'] = $request->number;
        $address['whatsapp'] = $request->whatsapp;
        $address['email'] = $request->email;
        $address['name'] = $request->name;
        $address['latitude'] = $request->latitude;
        $address['longitude'] = $request->longitude;

        if (session()->has('address')) {
            Session::forget('address');
            Session::put('address', $address);
        } else {
            Session::put('address', $address);
        }

        return redirect('step2');
    }

    public function timeSlotsSession(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'service_staff_id' => 'required',
        ]);

        $staff_and_time = [];

        $staff_and_time['date'] = $request->date;
        [$time_slot, $staff_id] = explode(":", $request->service_staff_id);
        $staff_and_time['time_slot'] =$time_slot;
        $staff_and_time['service_staff_id'] = $staff_id;

        if (session()->has('staff_and_time')) {
            Session::forget('staff_and_time');
            Session::put('staff_and_time', $staff_and_time);
        } else {
            Session::put('staff_and_time', $staff_and_time);
        }

        return redirect('step3');
    }

    public function step1()
    {
        if (Session::get('serviceIds')) {
            if (Auth::check()) {
                $email = Auth::user()->email;
                $name = Auth::user()->name;
            } else {
                $email = '';
                $name = '';
            }
            return view('site.checkOut.step1', compact('email', 'name'));
        } else {
            return redirect('/')->with('error', 'There is no Services in Your Cart.');
        }
    }

    public function step2(Request $request)
    {
        if (Session::get('address') && Session::get('serviceIds')) {
            $address = Session::get('address');
            $staffZoneNames = [$address['area'], $address['city']];

            $slots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
                $query->where(function ($query) use ($staffZoneNames) {
                    foreach ($staffZoneNames as $staffZoneName) {
                        $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                    }
                });
            })->where('date', '=', date("Y-m-d"))
                ->get();

            if (count($slots)) {
                $timeSlots = $slots;
            } else {
                $timeSlots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
                    $query->where(function ($query) use ($staffZoneNames) {
                        foreach ($staffZoneNames as $staffZoneName) {
                            $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                        }
                    });
                })->get();
            }

            $holiday = Holiday::where('date', date("Y-m-d"))->get();

            return view('site.checkOut.step2', compact('timeSlots', 'holiday'));
        } elseif (Session::get('serviceIds')) {
            return redirect('step1')->with('error', 'There is no Address Saved.');
        } else {
            return redirect('/')->with('error', 'There is no Services in Your Cart.');
        }
    }

    public function step3(Request $request)
    {
        if (Session::get('staff_and_time') && Session::get('address') && Session::get('serviceIds')) {

            $staff_and_time = Session::get('staff_and_time');
            $address = Session::get('address');
            $serviceIds = Session::get('serviceIds');

            foreach ($serviceIds as $id) {
                $services[] = Service::find($id);
            }

            $time_slot = TimeSlot::find($staff_and_time['time_slot']);

            $staff = User::find($staff_and_time['service_staff_id']);

            $i = 0;

            return view('site.checkOut.step3', compact('services', 'time_slot', 'address', 'staff', 'i', 'staff_and_time'));
        } elseif (Session::get('serviceIds') && Session::get('address')) {

            return redirect('step2')->with('error', 'There is no Time Slots Data Saved.');
        } elseif (Session::get('serviceIds')) {

            return redirect('step1')->with('error', 'There is no Address Saved.');
        } else {

            return redirect('/')->with('error', 'There is no Services in Your Cart.');
        }
    }

    public function slots(Request $request)
    {
        $address = Session::get('address');
            $staffZoneNames = [$address['area'], $address['city']];

            $slots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
                $query->where(function ($query) use ($staffZoneNames) {
                    foreach ($staffZoneNames as $staffZoneName) {
                        $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                    }
                });
            })->where('date', '=', $request->date)
                ->get();

            if (count($slots)) {
                $timeSlots = $slots;
            } else {
                $timeSlots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
                    $query->where(function ($query) use ($staffZoneNames) {
                        foreach ($staffZoneNames as $staffZoneName) {
                            $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                        }
                    });
                })->get();
            }

            $holiday = Holiday::where('date', $request->date)->get();
            return view('site.checkOut.timeSlots', compact('timeSlots', 'holiday'));

        // return response()->json($timeSlots);
    }
}
