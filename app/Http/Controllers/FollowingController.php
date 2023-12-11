<?php

namespace App\Http\Controllers;

use App\Models\Following;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $followings = Following::all();
        return view('followings.index', compact('followings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('followings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'auction' => 'required|exists:auction,id',
            'notifications' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'user' => 'required|exists:authenticated_user,id',
            // Add validation rules for other fields
        ]);

        Following::create($validatedData);

        return redirect()->route('followings.index')
            ->with('success', 'Following created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Following $following)
    {
        return view('followings.show', compact('following'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Following $following)
    {
        return view('followings.edit', compact('following'));
    }

    /**
     * Follow an auction.
     */
    public function follow($id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to follow auctions.');
        }

        // Find the auction by ID
        $auction = Auction::findOrFail($id);

        // Check if the user is already following the auction
        $isFollowing = $user->followings()->where('auction_id', $auction->id)->exists();

        if (!$isFollowing) {
            // Create a new following entry
            $user->followings()->create([
                'auction_id' => $auction->id,
                'notifications' => true, // Set to true or false based on your requirements
                'start_date' => now(), // Set the start date as needed
            ]);

            return redirect()->route('follow')->with('success', 'You are now following this auction.');
        } else {
            return redirect()->route('follow')->with('error', 'You are already following this auction.');
        }
    }

    /**
     * Unfollow an auction.
     */
    public function unfollow($id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to unfollow auctions.');
        }

        // Find the auction by ID
        $auction = Auction::findOrFail($id);

        // Check if the user is following the auction
        $following = $user->followings()->where('auction_id', $auction->id)->first();

        if ($following) {
            // Delete the following entry
            $following->delete();

            return redirect()->route('unfollow')->with('success', 'You have unfollowed this auction.');
        } else {
            return redirect()->route('unfollow')->with('error', 'You are not currently following this auction.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Following $following)
    {
        $validatedData = $request->validate([
            'auction' => 'required|exists:auction,id',
            'notifications' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'user' => 'required|exists:authenticated_user,id',
            // Add validation rules for other fields
        ]);

        $following->update($validatedData);

        return redirect()->route('followings.index')
            ->with('success', 'Following updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Following $following)
    {
        $following->delete();

        return redirect()->route('followings.index')
            ->with('success', 'Following deleted successfully');
    }
}
