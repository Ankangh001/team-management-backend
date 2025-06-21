<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Message;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        // Ensure logged-in user
        $senderId = Auth::id();

        // Check if already messaged the same team member (optional)
        $alreadyMessaged = Message::where('sender_id', $senderId)
            ->where('receiver_id', $request->receiver_id)
            ->exists();

        if ($alreadyMessaged) {
            return response()->json(['error' => 'You already sent a message to this team member.'], 400);
        }

        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject ?? null,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Message sent successfully.', 'data' => $message], 201);
    }

    // Team Member's message inbox
    public function inbox()
    {
        $userId = Auth::id();

        $messages = Message::with('sender')
            ->where('receiver_id', $userId)
            ->latest()
            ->get();

        return response()->json($messages);
    }

    public function myMessages()
    {
        return Message::where('receiver_id', auth()->id())
            ->with('sender')
            ->latest()
            ->get();
    }
}
