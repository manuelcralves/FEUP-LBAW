<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auctions = Auction::all();
        return view('auctions.index', compact('auctions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auctions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'required|date',
            'starting_price' => 'required|numeric',
            'current_price' => 'required|numeric',
            'status' => 'required|in:ACTIVE,CLOSED',
            'owner' => 'nullable|exists:authenticated_user,id',
            'item' => 'nullable|exists:item,id',
            // Add validation rules for other fields
        ]);

        Auction::create($validatedData);

        return redirect()->route('auctions.index')
            ->with('success', 'Auction created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Auction $auction)
    {
        return view('auctions.show', compact('auction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auction $auction)
    {
        return view('auctions.edit', compact('auction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auction $auction)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'required|date',
            'starting_price' => 'required|numeric',
            'current_price' => 'required|numeric',
            'status' => 'required|in:ACTIVE,CLOSED',
            'owner' => 'nullable|exists:authenticated_user,id',
            'item' => 'nullable|exists:item,id',
            // Add validation rules for other fields
        ]);

        $auction->update($validatedData);

        return redirect()->route('auctions.index')
            ->with('success', 'Auction updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auction $auction)
    {
        $auction->delete();

        return redirect()->route('auctions.index')
            ->with('success', 'Auction deleted successfully');
    }
}
