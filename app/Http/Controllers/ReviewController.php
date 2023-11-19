<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::all();
        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'required',
            'description' => 'required',
            // Add validation rules for other fields
        ]);

        Review::create($validatedData);

        return redirect()->route('reviews.index')
            ->with('success', 'Review created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validatedData = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'required',
            'description' => 'required',
            // Add validation rules for other fields
        ]);

        $review->update($validatedData);

        return redirect()->route('reviews.index')
            ->with('success', 'Review updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully');
    }
}
