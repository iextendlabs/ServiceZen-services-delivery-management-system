<?php

namespace App\Http\Controllers;

use App\Models\BidChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidChatController extends Controller
{
    public function fetchMessages($bid_id)
    {
        $messages = BidChat::where('bid_id', $bid_id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $bid_id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = BidChat::create([
            'bid_id' => $bid_id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}
