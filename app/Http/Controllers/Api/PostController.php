<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::select('id', 'title', 'content', 'post_type', 'is_pinned', 'author', 'image', 'created_at', 'likes')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        foreach ($posts as $post) {
            $post->liked_by = $post->liked_by ?? [];

            // Add full image URL if image exists
            if ($post->image) {
                $filename = basename($post->image);
                $post->image = url('/direct-post-image/' . $filename);
            } else {
                $post->image = null;
            }
        }

        return response()->json($posts);
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

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'post_type' => 'required|string|in:blog,event,news',
            'author' => 'required|string|max:255',
            'is_pinned' => 'nullable|boolean',
            'image' => 'nullable|image',
        ]);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->post_type = $request->post_type;
        $post->author = $request->author;
        $post->is_pinned = $request->is_pinned ?? false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->image = 'storage/' . $path;
        }

        $post->save();

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }

    public function like(Request $request, $id)
    {
        $user = auth()->user();
        $author = $user ? $user->name : $request->input('author');

        if (!$author) {
            return response()->json(['message' => 'Author name required'], 422);
        }

        // Check if this user already liked the post
        $existing = \App\Models\Like::where('post_id', $id)
            ->where('author', $author)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already liked this post'], 400);
        }

        // Optional: you can also store likes in a separate table for tracking
        \App\Models\Like::create([
            'post_id' => $id,
            'author' => $author,
        ]);

        // Increment the post's like count
        $post = Post::findOrFail($id);
        $post->increment('likes');

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $post = Post::with('comments')->findOrFail($id);

        if ($post->image) {
            $filename = basename($post->image);
            $post->image = url('/direct-post-image/' . $filename);
        } else {
            $post->image = null;
        }
        
        return response()->json($post);
    }

}
