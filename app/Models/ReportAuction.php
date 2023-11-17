<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAuction extends Model
{
    use HasFactory;

    protected $table = 'report_auction';
    public $timestamps = false;

    protected $fillable = [
        'reason',
        'creation_date',
        'user', // Foreign key for AuthenticatedUser
        'auction' // Foreign key for Auction
    ];

    protected $casts = [
        'creation_date' => 'datetime'
    ];
}
