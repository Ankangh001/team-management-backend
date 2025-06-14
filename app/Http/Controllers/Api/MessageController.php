<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Comment;
use App\Models\Post;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $alreadyMessaged = Message::where('sender_id', auth()->id())
            ->where('receiver_id', $request->receiver_id)
            ->exists();

        if ($alreadyMessaged) {
            return response()->json(['error' => 'You can only message one team member once.'], 403);
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        return response()->json($message, 201);
    }

    public function myMessages()
    {
        return Message::where('receiver_id', auth()->id())
            ->with('sender')
            ->latest()
            ->get();
    }

}
