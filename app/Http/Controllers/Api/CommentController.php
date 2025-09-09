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

    public function store_old(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = auth()->user();

        dd($request->all());
        $comment = new Comment();
        $comment->post_id = $id;
        $comment->content = $request->content;
        $comment->author = $user->name; // Securely get author from logged-in user
        $comment->user_id = $user->id;  // (If you have `user_id` field)
        $comment->likes = 0;
        $comment->save();

        return response()->json($comment, 201);
    }


    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
            'author' => 'nullable|string', // optional if user is logged in
        ]);

        $user = auth()->user();
        $author = $user ? $user->name : $request->author;

        if (!$author) {
            return response()->json(['message' => 'Author name required'], 422);
        }

        // Optional: prevent one comment per user logic
        $existing = \App\Models\Comment::where('post_id', $postId)
            ->where('author', $author)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already commented on this post'], 400);
        }

        $comment = \App\Models\Comment::create([
            'post_id' => $postId,
            'author' => $author,
            'content' => $request->content,
        ]);

        return response()->json($comment);
    }

    public function reply(Request $request, $commentId)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $comment = Comment::findOrFail($commentId);

        if ($comment->reply) {
            return response()->json(['message' => 'Reply already exists'], 400);
        }

        $comment->reply = $request->reply;
        $comment->save();

        return response()->json(['message' => 'Reply saved successfully']);
    }

}
