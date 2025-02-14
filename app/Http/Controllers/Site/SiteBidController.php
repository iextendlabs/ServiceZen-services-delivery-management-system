<?php

namespace App\Http\Controllers\Site;

use App\Models\Bid;
use App\Models\BidChat;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class SiteBidController extends Controller
{
    public function index($quote_id)
    {
        $quote = Quote::findOrFail($quote_id);
        $bids = Bid::where('quote_id', $quote_id)->with('staff')->get();
        
        return view('site.bids.index', compact('quote', 'bids'));
    }
    public function showBidPage($quote_id, $staff_id)
    {
        $quote = Quote::findOrFail($quote_id);

        // Check if the staff has already placed a bid
        $bid = Bid::where('quote_id', $quote_id)
            ->where('staff_id', $staff_id)
            ->first();

        return view('site.bids.show', compact('quote', 'bid', 'staff_id'));
    }  
}
