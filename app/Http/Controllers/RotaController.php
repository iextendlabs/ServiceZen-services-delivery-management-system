<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RotaController extends Controller
{
    public function index(Request $request) {
        if($request->has('date')) {
            $currentDate = $request->input('date');
        } else {
            $currentDate = Carbon::now()->toDateString();
        }

        $currentUser = Auth::user();

        $query = Order::where('date', $currentDate);
        $userRole = $currentUser->getRoleNames()->first(); // Assuming you have a variable that holds the user's role, e.g., $userRole = $currentUser->getRole();

        switch ($userRole) {
            case 'Manager':
                $staffIds = $currentUser->getManagerStaffIds();
                $query = $query->whereIn('service_staff_id', $staffIds)->orderBy('date', 'ASC')->orderBy('time_start');
                break;

            case 'Supervisor':
                $staffIds = $currentUser->getSupervisorStaffIds();
                $query = $query->whereIn('service_staff_id', $staffIds)
                    ->where(function ($query) {
                        $query->whereDoesntHave('cashCollection');
                    })
                    ->orderBy('date', 'ASC')->orderBy('time_start');
                break;

            case 'Staff':
                $query = $query->where('service_staff_id', Auth::id())
                ->orderBy('date', 'ASC')
                ->orderBy('time_start')
                ->where('date', '=', $currentDate)
                ->where(function ($query) {
                    $query->whereIn('status', ['Complete', 'Confirm', 'Accepted'])
                        ->whereDoesntHave('cashCollection');
                });
                
                break;

            default:
                $query;
                break;
        }
        $orders = $query->get();
        return view('graph.index', [
            'orders' => $orders,
            'date' => $currentDate
        ]);
    }
}
