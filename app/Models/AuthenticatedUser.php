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

    
    public function addresses() {
        return $this->hasMany(Address::class);
    }
    
    public function bids() {
        return $this->hasMany(Bid::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function reviews_written() {
        return $this->hasMany(Review::class, 'reviewer');
    }
    
    public function reviews_received() {
        return $this->hasMany(Review::class, 'reviewed');
    }
    

    public function notifications() {
        return $this->belongsToMany(Notification::class);
    }

    public function report_auctions() {
        return $this->hasMany(ReportAuction::class);
    }

    public function auctions() {
        return $this->hasMany(Auction::class);
    }

    public function following_auctions() {
        return $this->belongsToMany(Auction::class, 'following', 'user', 'auction')
                    ->withPivot('notifications', 'start_date');
    }
}
