<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'post_type',
        'author',
        'is_pinned',
    ];
}
