<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $table = 'auction';

    public $timestamps = false;

    protected $fillable = [
        'title', 
        'description', 
        'start_date', 
        'end_date', 
        'starting_price', 
        'current_price', 
        'status', 
        'owner', // Foreign Key for the AuthenticatedUser model
        'item'   // Foreign Key for the Item model
    ];

    protected $casts = [
        'starting_price' => 'float',
        'current_price' => 'float',
        'status' => 'string' 
    ];

    public function item() {
        return $this->hasOne(Item::class, 'item');
    }

    public function review() {
        return $this->hasOne(Review::class, 'auction');
    }

    public function bids() {
        return $this->hasMany(Bid::class, 'auction');
    }

    public function reportAuctions() {
        return $this->hasMany(ReportAuction::class, 'auction');
    }

    public function authenticatedUser() {
        return $this->belongsTo(AuthenticatedUser::class, 'owner');
    }

    public function followers() {
        return $this->belongsToMany(AuthenticatedUser::class, 'following', 'auction', 'user')
                    ->withPivot('notifications', 'start_date');
    }   
}
