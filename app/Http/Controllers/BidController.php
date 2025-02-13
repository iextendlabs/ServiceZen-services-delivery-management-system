<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\BidChat;
use App\Models\Quote;
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

        Bid::create([
            'quote_id' => $quote_id,
            'staff_id' => $staff_id,
            'bid_amount' => $request->bid_amount,
            'comment' => $request->comment,
        ]);

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
            'message' => "Bid updated to $" . $request->bid_amount . ".",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Bid updated successfully.",
            'new_bid_amount' => $request->bid_amount,
        ]);
    }   
}
