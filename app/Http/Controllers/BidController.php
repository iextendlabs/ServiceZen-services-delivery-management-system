<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\BidChat;
use App\Models\BidImage;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function index($quote_id)
    {
        $quote = Quote::findOrFail($quote_id);
        $bids = Bid::where('quote_id', $quote_id)->with('staff')->get();

        return view('bids.index', compact('quote', 'bids'));
    }
    public function showBidPage($quote_id, $staff_id)
    {
        $quote = Quote::findOrFail($quote_id);

        // Check if the staff has already placed a bid
        $bid = Bid::where('quote_id', $quote_id)
            ->where('staff_id', $staff_id)
            ->first();

        return view('bids.show', compact('quote', 'bid', 'staff_id'));
    }

    public function store(Request $request, $quote_id, $staff_id)
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:1',
            'comment' => 'nullable|string|max:255',
        ]);

        $quote = Quote::find($quote_id);

        $staff = User::find($staff_id);

        $bid = Bid::create([
            'quote_id' => $quote_id,
            'staff_id' => $staff_id,
            'bid_amount' => $request->bid_amount,
            'comment' => $request->comment,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('quote-images/bid-images'), $filename);

                BidImage::create([
                    'bid_id' => $bid->id,
                    'image' => $filename
                ]);
            }
        }

        if ($quote->user) {
            $quote->user->notifyOnMobile("Bid", 'A bid has been created for your quote by staff member ' . $staff->name);
        }

        return redirect()->route('quote.bid', ['quote_id' => $quote_id, 'staff_id' => $staff_id])
            ->with('success', 'Bid submitted successfully.');
    }

    public function updateBid(Request $request, $bid_id)
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:1',
        ]);

        $bid = Bid::findOrFail($bid_id);
        $bid->bid_amount = $request->bid_amount;
        $bid->save();

        // Add a message in the chat about the update
        BidChat::create([
            'bid_id' => $bid_id,
            'sender_id' => Auth::id(),
            'message' => "Bid updated to AED" . $request->bid_amount . ".",
        ]);

        if ($bid->quote && $bid->quote->user) {
            $bid->quote->user->notifyOnMobile("Bid Chat on quote#" . $bid->quote_id, "Bid updated to AED" . $request->bid_amount . ".");
        }

        return response()->json([
            'success' => true,
            'message' => "Bid updated successfully.",
            'new_bid_amount' => $request->bid_amount,
        ]);
    }
}
