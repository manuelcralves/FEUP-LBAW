<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticatedUser extends Model
{
    use HasFactory;

    protected $table = 'authenticated_user';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'rating',
        'picture',
        'balance',
        'is_blocked',
        'role'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'rating' => 'float',
        'balance' => 'float', 
        'is_blocked' => 'boolean'
    ];
}
