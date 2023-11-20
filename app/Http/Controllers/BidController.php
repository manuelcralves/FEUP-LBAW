<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Auction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function myBids($id, $pageNr)
    {
        // Retrieve the authenticated user
        $user = Auth::user();
    
        if (!$user) {
            return abort(403);
        }
    
        // Define the number of bids to display per page
        $perPage = 5;
    
        // Calculate the offset based on the page number
        $offset = ($pageNr - 1) * $perPage;
    
        // Retrieve bids with associated auctions and paginate them
        $bids = Bid::with('auctions')
            ->where('user', $user->id) // Replace 'user_id_column_name' with the actual column name
            ->orderBy('creation_date', 'desc') // Order by most recent
            ->paginate($perPage, ['*'], 'page', $pageNr); // Paginate with the specified per page limit and page number
    
        // Pass the $id variable and the current page number to the view
        return view('pages.my_bids', compact('bids', 'id', 'pageNr'));
    }    

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bid $bid)
    {
        //
    }
}
