<?php

namespace App\Http\Controllers;

use App\Models\Bid;
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
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:2048'
        ]);

        $messageData = [
            'bid_id' => $bid_id,
            'sender_id' => Auth::id(),
            'file' => 0,
        ];

        $bid = Bid::findOrFail($bid_id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = mt_rand() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('quote-images/bid-chat-files'), $filename);
    
            $messageData['message'] = $filename;
            $messageData['file'] = 1;
        } else {
            $messageData['message'] = $request->message;
        }

        $message = BidChat::create($messageData);

        $message = $messageData['file'] == 1 ? "There is a file uploaded" : $messageData['message'];
        $usersToNotify = [];

        if (auth()->user()->hasRole('Staff') && $bid->quote?->user) {
            $usersToNotify[] = $bid->quote->user;
        }

        if (auth()->user()->hasRole('Customer') && $bid->staff) {
            $usersToNotify[] = $bid->staff;
        }

        if (auth()->user()->hasRole('Admin')) {
            if ($bid->staff) {
                $usersToNotify[] = $bid->staff;
            }
            if ($bid->quote?->user) {
                $usersToNotify[] = $bid->quote->user;
            }
        }

        foreach ($usersToNotify as $user) {
            $user->notifyOnMobile("Bid Chat on quote#{$bid->quote_id}", $message);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}
