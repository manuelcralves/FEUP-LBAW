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
        'user_id', // Foreign key for AuthenticatedUser
        'auction_id' // Foreign key for Auction
    ];

    protected $casts = [
        'creation_date' => 'datetime'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class, 'auction_id');  
    }

    public function authenticatedUser() {
        return $this->belongsTo(AuthenticatedUser::class, 'user_id');  
    }
}
