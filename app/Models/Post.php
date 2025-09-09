<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'post_type',
        'author',
        'is_pinned',
        'image', // ✅ include this
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
