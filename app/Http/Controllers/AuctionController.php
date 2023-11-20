<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($pageNr = 1)
    {
        $perPage = 5; // Number of auctions per page
    
        // Create a custom paginator with the desired URL structure
        $auctions = Auction::paginate($perPage, ['*'], 'page', $pageNr);
    
        // Set the path for the paginator to use the named route
        $auctions->withPath(route('auction.index', ['pageNr' => $pageNr]));
    
        return view('pages.auctions', compact('auctions'));
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
    public function show($id)
    {
        $auction = Auction::findOrFail($id);
    
        // Load the associated item
        $item = $auction->item;
    
        return view('pages.showauction', compact('auction', 'item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auction $auction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auction $auction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auction $auction)
    {
        //
    }
}
