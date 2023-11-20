<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuthenticatedUser;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $pageNr = 1)
    {
        $perPage = 5; // Number of auctions per page
        $query = $request->input('query'); // Get the search query from the request
    
        // Initialize a query builder for auctions
        $auctionsQuery = Auction::query();
    
        // If a search query is provided, filter auctions based on the search criteria
        if ($query) {
            $auctionsQuery->whereRaw("tsvectors @@ to_tsquery('english', ?)", [$query]);
        }
    
        // Paginate the results
        $auctions = $auctionsQuery->paginate($perPage, ['*'], 'page', $pageNr);
    
        // Set the path for the paginator to use the named route with query parameter
        $auctions->appends(['query' => $query])->links();
    
        return view('pages.auctions', compact('auctions', 'query'));
    }    

    public function showOwnedAuctions($id, $pageNr)
    {
        // Retrieve the authenticated user
        $user = Auth::user();
    
        if (!$user) {
            return abort(403);
        }
    
        // Retrieve the user by the provided $id
        $targetUser = AuthenticatedUser::find($id);
    
        if (!$targetUser) {
            return abort(404);
        }
    
        // Define the number of auctions to display per page
        $perPage = 5;
    
        // Calculate the offset based on the page number
        $offset = ($pageNr - 1) * $perPage;
    
        // Retrieve the auctions where the authenticated user is the owner and paginate them
        $ownedAuctions = $targetUser->auctions()
            ->orderBy('start_date', 'desc') 
            ->paginate($perPage, ['*'], 'page', $pageNr); // Paginate with the specified per page limit and page number
    
        return view('pages.owned_auctions', compact('ownedAuctions', 'id', 'pageNr'));
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
