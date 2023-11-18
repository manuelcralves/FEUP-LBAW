<?php

namespace App\Http\Controllers;

use App\Models\Following;
use Illuminate\Http\Request;

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
            // Add validation rules for following fields
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Following $following)
    {
        $validatedData = $request->validate([
            // Add validation rules for following fields
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
