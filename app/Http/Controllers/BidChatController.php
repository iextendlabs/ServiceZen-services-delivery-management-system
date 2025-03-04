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
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:2048'
        ]);

        $messageData = [
            'bid_id' => $bid_id,
            'sender_id' => Auth::id(),
            'file' => 0,
        ];

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

        return response()->json(['success' => true, 'message' => $message]);
    }
}
