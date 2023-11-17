<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Following extends Pivot
{
    use HasFactory;

    protected $table = 'following';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'auction', // Foreign key for Auction
        'notifications',
        'start_date',
        'user' // Foreign key for AuthenticatedUser
    ];

    protected $casts = [
        'notifications' => 'boolean',
        'start_date' => 'datetime'
    ];
}
