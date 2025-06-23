<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::select('id', 'title', 'content', 'post_type', 'is_pinned', 'author', 'image', 'created_at')
        ->orderByDesc('is_pinned')
        ->orderByDesc('created_at')
        ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'post_type' => 'required|in:blog,news,event',
            'author' => 'required|string',
            'is_pinned' => 'boolean',
            'image' => 'nullable|image|max:2048', // 👈 validate image
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public'); // stored in storage/app/public/posts
        }

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'post_type' => $request->post_type,
            'author' => $request->author,
            'is_pinned' => $request->is_pinned,
            'image' => $path,
        ]);

        return response()->json($post);
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

    public function show($id)
    {
        $post = Post::with('comments')->findOrFail($id);

        return response()->json($post);
    }

}
