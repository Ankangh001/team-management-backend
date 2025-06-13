<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'post_type' => 'required|in:blog,event',
            'author' => 'nullable|string',
            'is_pinned' => 'boolean',
        ]);

        $post = Post::create($data);

        return response()->json($post, 201);
    }
}
