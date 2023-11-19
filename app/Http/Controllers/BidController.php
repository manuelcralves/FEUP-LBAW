<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;

class BidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bids = Bid::all();
        return view('bids.index', compact('bids'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bids.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'value' => 'required|numeric',
            'creation_date' => 'nullable|date',
            'user' => 'nullable|exists:authenticated_user,id',
            'auction' => 'nullable|exists:auction,id',
            // Add validation rules for other fields
        ]);

        Bid::create($validatedData);

        return redirect()->route('bids.index')
            ->with('success', 'Bid created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bid $bid)
    {
        return view('bids.show', compact('bid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bid $bid)
    {
        return view('bids.edit', compact('bid'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bid $bid)
    {
        $validatedData = $request->validate([
            'value' => 'required|numeric',
            'creation_date' => 'nullable|date',
            'user' => 'nullable|exists:authenticated_user,id',
            'auction' => 'nullable|exists:auction,id',
            // Add validation rules for other fields
        ]);

        $bid->update($validatedData);

        return redirect()->route('bids.index')
            ->with('success', 'Bid updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bid $bid)
    {
        $bid->delete();

        return redirect()->route('bids.index')
            ->with('success', 'Bid deleted successfully');
    }
}
