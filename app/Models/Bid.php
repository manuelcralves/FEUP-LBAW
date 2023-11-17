<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $table = 'bid';

    public $timestamps = false;

    protected $fillable = [
        'value', 
        'creation_date', 
        'user',  // Foreign Key for the AuthenticatedUser model
        'auction' // Foreign Key for the Auction model
    ];

    protected $casts = [
        'value' => 'float',
        'creation_date' => 'datetime'
    ];
}
