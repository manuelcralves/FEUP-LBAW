<?php

namespace App\Http\Controllers;
use App\Models\AuthenticatedUser;

use App\Models\Bid;
use Illuminate\Http\Request;

class BidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Bid $bid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bid $bid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bid $bid)
{   
    // Get the bid amount from the form submission
    $bidAmount = $request->input('bid_amount');
    echo $request;
    $username = auth()->user()->username; // Fetch username from authenticated user
    $rating = auth()->user()->rating;
    
    // Perform actions with bid, then return the data as JSON
    $data = [
        'place' => 'Some place', // Update with actual bid place
        'rating' => $rating, // Update with actual bid rating
        'username' => $username,
        'bid_amount' => $bidAmount,
    ];
    
    return response()->json($data);
}


    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bid $bid)
    {
        //
    }
}
