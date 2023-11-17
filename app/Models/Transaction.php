<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

    public $timestamps = false;

    protected $fillable = [
        'value', 
        'transaction_date', 
        'description', 
        'user' // Foreign Key for the AuthenticatedUser model
    ];

    protected $casts = [
        'value' => 'float', 
        'transaction_date' => 'datetime'
    ];
}
