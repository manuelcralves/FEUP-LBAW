<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';

    public $timestamps = false;

    protected $fillable = [
        'street', 
        'postal_code', 
        'city', 
        'country', 
        'user' // Foreign Key for the AuthenticatedUser model
    ];

    public function authenticated_user() {
        return $this->belongsTo(AuthenticatedUser::class, 'user');
    }
}
