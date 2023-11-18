<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'review';
    public $timestamps = false;

    protected $fillable = [
        'rating',
        'title',
        'description',
        'date',
        'reviewer_id', // Foreign key for AuthenticatedUser
        'reviewed_id', // Foreign key for AuthenticatedUser
        'auction_id'   // Foreign key for Auction
    ];

    protected $casts = [
        'rating' => 'int',
        'date' => 'datetime'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class, 'auction_id');
    }

    public function reviewer() {
        return $this->belongsTo(AuthenticatedUser::class, 'reviewer_id');
    }

    public function reviewed() {
        return $this->belongsTo(AuthenticatedUser::class, 'reviewed_id');
    }
}
