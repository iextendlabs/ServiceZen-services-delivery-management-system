<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Quote;
use App\Models\QuoteStaff;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StaffGroup;
use App\Models\StaffZone;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:quote-list|quote-create|quote-edit|quote-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:quote-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:quote-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:quote-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $filter = [
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'status' => $request->status,
            'source' => $request->source
        ];

        $query = Quote::latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }

        if (Auth::user()->hasRole('Staff')) {
            $query->whereHas('staffs', function ($q) {
                $q->where('staff_id', Auth::id()); // Filter by logged-in staff
            });
        }

        $total_quote = $query->count();

        $quotes = $query->paginate(config('app.paginate'));

        $filters = $request->only(['user_id', 'service_id', 'status']);
        $quotes->appends($filters);
        $users = User::role('Customer')->where('status', 1)->get();

        $staffGroups = StaffGroup::all();
        $staffZones = StaffZone::all();
        $quote_statuses = config('app.quote_status');
        $staffs = User::role('Staff')->where('status', 1)->get();
        $services = Service::where('status', 1)->get();
        $categories = ServiceCategory::where('status', 1)->get();

        $sources = ['CRM', 'App', 'Web'];
        return view('quotes.index', compact('quotes', 'filter', 'total_quote', 'users', 'services', 'categories', 'quote_statuses', 'staffs', 'staffGroups', 'staffZones','sources'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quote = Quote::with(['bids.staff'])->findOrFail($id);

        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quote = Quote::find($id);
        if ($quote->images) {
            foreach ($quote->images as $images) {
                if (file_exists(public_path('quote-images') . '/' . $images->image)) {
                    unlink(public_path('quote-images') . '/' . $images->image);
                }
            }
        }
        $quote->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Quote deleted successfully');
    }

    public function bulkStatusEdit(Request $request)
    {
        $selectedItems = $request->input('selectedItems');
        $status = $request->input('status');

        if (!empty($selectedItems)) {

            foreach ($selectedItems as $id) {
                $quote = Quote::findOrFail($id);
                $quote->status = $status;
                $quote->save();
            }

            return response()->json(['message' => 'Selected quote status updated successfully.']);
        } else {
            return response()->json(['message' => 'No quote selected.']);
        }
    }

    public function bulkAssignStaff(Request $request)
    {
        $selectedItems = $request->input('selectedItems');
        $selectedStaffs = $request->input('selectedStaffs');

        if (empty($selectedItems) || empty($selectedStaffs)) {
            return response()->json(['message' => 'No staff or quotes selected.'], 400);
        }

        foreach ($selectedItems as $quote_id) {
            $quote = Quote::find($quote_id);

            if (!$quote) {
                continue; // Skip if the quote does not exist
            }

            foreach ($selectedStaffs as $staffData) {
                $staff_id = $staffData['staff_id'];
                $staff = User::find($staff_id);
                $staff->notifyOnMobile('Quote', 'A new quote has been generated with ID: ' . $quote->id, null, 'Staff App');
                $quote_amount = $staffData['quote_amount'];
                $quote_commission = $staffData['quote_commission'];

                $quote->staffs()->syncWithoutDetaching([
                    $staff_id => [
                        'status' => 'Pending',
                        'quote_amount' => $quote_amount,
                        'quote_commission' => $quote_commission
                    ]
                ]);
            }
        }

        return response()->json(['message' => 'Quotes assigned successfully.']);
    }

    public function detachStaff($quoteId, $staffId)
    {
        $quote = Quote::findOrFail($quoteId);
        $quote->staffs()->detach($staffId);

        if ($quote->bid && $quote->bid->staff_id == $staffId) {
            $quote->status = "Pending";
            $quote->bid_id = null;
            $quote->save();
        }

        Bid::where('staff_id', $staffId)->where('quote_id', $quoteId)->delete();

        return redirect()->back()->with('success', 'Staff removed successfully.');
    }

    public function updateStatus(Request $request)
    {
        $quote = Quote::findOrFail($request->id);
        $user = auth()->user();

        $staffQuote = $quote->staffs->firstWhere('id', $user->id);

        if (!$staffQuote) {
            return response()->json(['error' => 'Staff quote not found.'], 404);
        }

        if ($request->status == "Rejected") {
            $quote->staffs()->updateExistingPivot($user->id, ['status' => $request->status]);
        } else {
            $staff_total_balance = Transaction::where('user_id', $user->id)->sum('amount');

            if ($staffQuote->pivot->quote_amount < 0 && $staff_total_balance < abs($staffQuote->pivot->quote_amount)) {
                return response()->json([
                    'error' => 'You do not have enough balance to accept this quote. Please add funds to your balance.'
                ], 400);
            }

            $quote->staffs()->updateExistingPivot($user->id, ['status' => $request->status]);
            if($staffQuote->pivot->quote_amount !== null) {
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $staffQuote->pivot->quote_amount,
                    'type' => 'Quote',
                    'status' => 'Approved',
                    'description' => "Quote amount for quote ID: $quote->id"
                ]);
            }
        }

        return response()->json(['message' => 'Quote staff status updated successfully.']);
    }

    public function updateStaffData(Request $request)
    {
        $staffId = $request->staff_id;
        $quoteId = $request->quote_id;
        $field = $request->field;
        $value = $request->value;

        if (!in_array($field, ['quote_amount', 'quote_commission'])) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        QuoteStaff::where('quote_id', $quoteId)->where('staff_id', $staffId)->update([$field => $value]);

        return response()->json(['message' => 'Updated successfully']);
    }
}
