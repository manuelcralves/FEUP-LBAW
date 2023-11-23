<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuthenticatedUser;
use App\Models\Item;
use App\Policies\AuctionPolicy;

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
    
        // Add a condition to filter auctions with 'status' as "ACTIVE"
        $auctionsQuery->where('status', 'ACTIVE');
    
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
        return view('pages.auctionCreate'); 
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                // Handle unauthenticated user scenario
                return redirect()->route('login'); // Redirect to login or another appropriate response
            }
    
            $user = Auth::user();
    
            // Combine validation rules for both Auction and Item
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'end_date' => 'required|date|after:start_date',
                'starting_price' => 'required|numeric|min:0',
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'condition' => 'required|string|max:255',
            ]);
    
            $item = new Item($validatedData);
            $item->save();
            
            $auction = new Auction($validatedData);
            $auction->current_price = $validatedData['starting_price'];
            $auction->owner = $user->id;
            $auction->items()->associate($item); // Associate the auction with the item
            $auction->save();            
    
            return redirect()->route('auction.show', ['id' => $auction->id]);
        } catch (\Illuminate\Database\QueryException $ex) {
            // Capture the SQL error message
            $errorMessage = $ex->getMessage();
    
            // Regular expression to find text between "ERROR:" and "CONTEXT"
            $pattern = '/ERROR:(.*?)CONTEXT/';
            if (preg_match($pattern, $errorMessage, $matches)) {
                // The custom message is in $matches[1]
                $customMessage = trim($matches[1]);
            } else {
                $customMessage = 'An unexpected error occurred. Please try again.';
            }
    
            return redirect()->back()->with('error', $customMessage);
        }
    }    

    public function edit($id)
    {
        if (!Auth::check()) {
            // Handle unauthenticated user scenario
            return redirect()->route('login'); // Redirect to login or another appropriate response
        }
    
        $auction = Auction::findOrFail($id);
        $item = $auction->item; // Assuming a one-to-one relationship between Auction and Item
    
        // Return the edit view with the auction and item data
        return view('pages.auctionEdit', compact('auction', 'item'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            // Handle unauthenticated user scenario
            return redirect()->route('login');
        }
    
        $user = Auth::user();
    
        $auction = Auction::findOrFail($id);
    
        // Only update fields that can be changed
        $validatedAuctionData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
    
        // Update the Auction
        $auction->fill($validatedAuctionData);
        $auction->save();
    
        return redirect()->route('auction.show', ['id' => $auction->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $auction = Auction::findOrFail($id);
    
        // Use the authorize method to check if the user is authorized to view the auction
        $this->authorize('viewAuction', $auction);
    
        // Order the bids by value in descending order for this auction
        $auction->load(['bids' => function ($query) {
            $query->orderBy('value', 'desc');
        }]);
    
        // Load the associated item (if necessary)
        $item = Item::findOrFail($auction->item);
    
        return view('pages.showauction', compact('auction', 'item'));
    }

    public function cancel($id)
    {
        try {
            $auction = Auction::findOrFail($id);
        
            // Check if the user has the permission to cancel the auction (e.g., only the owner can cancel it)
            if (Auth::user()->id != $auction->owner) {
                return redirect()->back()->with('error', 'You do not have permission to cancel this auction.');
            }
        
            // Update the status to "CANCELLED"
            $auction->status = 'CANCELLED';
            $auction->save();
        
            return redirect()->route('auction.show', $id)->with('success', 'The auction has been canceled.');

        } catch (\Illuminate\Database\QueryException $ex) {
            // Capture the SQL error message
            $errorMessage = $ex->getMessage();
    
            // Regular expression to find text between "ERROR:" and "CONTEXT"
            $pattern = '/ERROR:(.*?)CONTEXT/';
            if (preg_match($pattern, $errorMessage, $matches)) {
                // The custom message is in $matches[1]
                $customMessage = trim($matches[1]);
            } else {
                $customMessage = 'An unexpected error occurred. Please try again.';
            }
    
            return redirect()->back()->with('error', $customMessage);
        }
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auction $auction)
    {
        //
    }
}
