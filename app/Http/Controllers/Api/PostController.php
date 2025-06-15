<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::select('id', 'title', 'content', 'post_type', 'is_pinned', 'author', 'created_at')
        ->orderByDesc('is_pinned')
        ->orderByDesc('created_at')
        ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'post_type' => 'required|in:blog,event,news',
            'author' => 'nullable|string',
            'is_pinned' => 'boolean',
        ]);

        $post = Post::create($data);

        return response()->json($post, 201);
    }

    public function stats()
    {
        $totalPosts = Post::count();
        $totalLikes = Post::sum('likes'); // (Assuming you add a `likes` column soon)
        $totalComments = 0; // Placeholder, implement once comments are added
        $totalViews = 0;    // Placeholder for views

        return response()->json([
            'total_posts' => $totalPosts,
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'total_views' => $totalViews,
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'post_type' => 'required|in:blog,event',
            'author' => 'nullable|string',
            'is_pinned' => 'boolean',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }

    public function like($id)
    {
        $post = Post::findOrFail($id);
        $post->increment('likes');
        return response()->json(['success' => true]);
    }

}
