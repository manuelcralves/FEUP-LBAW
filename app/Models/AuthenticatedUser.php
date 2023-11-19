<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AuthenticatedUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'role',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'rating' => 'float',
        'balance' => 'float', 
        'is_blocked' => 'boolean',
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

    public function reviewsWritten() {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
    
    public function reviewsReceived() {
        return $this->hasMany(Review::class, 'reviewed_id');
    }

    public function notifications() {
        return $this->belongsToMany(Notification::class, 'notification_user', 'user_id', 'notification_id');
    }

    public function reportAuctions() {
        return $this->hasMany(ReportAuction::class);
    }

    public function auctions() {
        return $this->hasMany(Auction::class, 'owner_id');
    }

    public function followingAuctions() {
        return $this->belongsToMany(Auction::class, 'following', 'user_id', 'auction_id')
                    ->withPivot('notifications', 'start_date');
    }
}
