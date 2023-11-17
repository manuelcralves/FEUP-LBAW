<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    public $timestamps = false;

    protected $fillable = [
        'message', 
        'type', 
        'creation_date', 
        'read', 
        'user' // Foreign Key for the AuthenticatedUser model
    ];

    protected $casts = [
        'read' => 'boolean',
        'type' => 'string', 
        'creation_date' => 'datetime'
    ];
}
