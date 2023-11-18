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
        'user_id',  // Foreign Key for the AuthenticatedUser model
        'auction_id' // Foreign Key for the Auction model
    ];

    protected $casts = [
        'value' => 'float',
        'creation_date' => 'datetime'
    ];

    public function authenticatedUser() {
        return $this->belongsTo(AuthenticatedUser::class, 'user_id');
    }

    public function auction() {
        return $this->belongsTo(Auction::class, 'auction_id');
    }
}
