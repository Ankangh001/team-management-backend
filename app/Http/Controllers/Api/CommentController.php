<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{

    public function index($id)
    {
        return Comment::where('post_id', $id)->orderBy('created_at')->get();
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = auth()->user();

        $comment = new Comment();
        $comment->post_id = $id;
        $comment->content = $request->content;
        $comment->author = $user->name; // Securely get author from logged-in user
        $comment->user_id = $user->id;  // (If you have `user_id` field)
        $comment->likes = 0;
        $comment->save();

        return response()->json($comment, 201);
    }


}
