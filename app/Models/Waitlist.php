<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    public $timestamps = false;
    protected $table = 'waitlist';
    protected $fillable = [
        'name', 'email', 'phone', 'interest', 'submitted_at'
    ];
}
