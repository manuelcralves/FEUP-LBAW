<?php

namespace App\Http\Controllers;

use App\Models\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Policies\AuthenticatedUserPolicy;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;

class AuthenticatedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch auctions with the highest current price
        $topAuctions = Auction::orderBy('current_price', 'desc')->take(5)->get();

        // Fetch top bidders
        $topBidders = Bid::select('user', DB::raw('COUNT(*) as total_bids'), DB::raw('SUM(value) as total_bid_amount'))
                         ->groupBy('user')
                         ->orderBy('total_bid_amount', 'desc')
                         ->take(5)
                         ->with('authenticatedUser')
                         ->get();

        return view('pages.home', compact('topAuctions', 'topBidders'));
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
        // Validate the request data
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:authenticated_user',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:authenticated_user',
            'password' => 'required|string|min:8'
        ]);

        // Hash the password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Create a new user with hashed password
        $user = AuthenticatedUser::create($validatedData);

        // Redirect or return a response as needed
        return redirect()->route('login')->with('success', 'User created successfully');
    }

    public function promoteToAdmin($id)
    {
        // Retrieve the user based on the $id parameter
        $user = AuthenticatedUser::find($id);
    
        if (!$user) {
            // Handle the case where the user is not found (e.g., show an error message)
            return abort(404);
        }
    
        // Check if the authenticated user has permission to promote users to ADMIN
        if (Auth::user()->role === 'ADMIN' && Auth::user()->id != $user->id) {
            // Check if the user has any "ACTIVE" auctions
            $hasActiveAuctions = $user->auctions()->where('status', 'ACTIVE')->exists();
    
            // Check if the user has any active bids
            $hasActiveBids = $user->bids()->whereHas('auctions', function ($query) {
                $query->where('status', 'ACTIVE');
            })->exists();
    
            if (!$hasActiveAuctions && !$hasActiveBids) {
                // User doesn't have active auctions or active bids, so promote them to ADMIN
                $user->role = 'ADMIN';
                $user->save();
    
                return back()->with('success', 'User promoted to ADMIN successfully');
            } else {
                // Handle the case where the user has active auctions or active bids
                return back()->with('error', 'User has active auctions or active bids and cannot be promoted to ADMIN.');
            }
        } else {
            // Handle the case where the authenticated user doesn't have permission
            return abort(403); // Return a 403 Forbidden response
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = AuthenticatedUser::find($id);
    
        if (!$user) {
            return abort(404);
        }
    
        $this->authorize('viewProfile', $user);
    
        return view('pages.profile', compact('user'));
    }

    public function searchResults(Request $request)
    {
        $perPage = 5;
        $query = $request->input('query');

        // Query users based on the search criteria if a query is provided
        $usersQuery = AuthenticatedUser::query();
        
        if ($query) {
            $usersQuery->where('username', 'LIKE', '%' . $query . '%')
            ->orWhere('first_name', 'LIKE', '%' . $query . '%')
            ->orWhere('last_name', 'LIKE', '%' . $query . '%')
            ->orWhere('email', 'LIKE', '%' . $query . '%');
        }

        // Query auctions based on the search criteria if a query is provided
        $auctionsQuery = Auction::query();

        if ($query) {
            $auctionsQuery->whereRaw("tsvectors @@ to_tsquery('english', ?)", [$query]);
        }

        // Add a condition to filter auctions with 'status' as "ACTIVE"
        $auctionsQuery->where('status', 'ACTIVE');

        // Paginate the results for both users and auctions
        $users = $usersQuery->paginate($perPage, ['*'], 'users_page');
        $auctions = $auctionsQuery->paginate($perPage, ['*'], 'auctions_page');

        // Return the search results view
        return view('pages.search_results', compact('users', 'auctions', 'query'));
    }
      

    public function all(Request $request, $pageNr)
    {
        $perPage = 5; // Number of users per page
        $query = $request->input('query'); // Get the search query from the request
    
        // Query users based on the search criteria if a query is provided
        $usersQuery = AuthenticatedUser::query();
        
        if ($query) {
            $usersQuery->where('username', '=', $query)
                       ->orWhere('first_name', '=', $query)
                       ->orWhere('last_name', '=', $query)
                       ->orWhere('email', '=', $query);
        }
        
        // Paginate the results
        $users = $usersQuery->paginate($perPage, ['*'], 'page', $pageNr);
    
        return view('pages.all_users', compact('users', 'query'));
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Retrieve the user based on the $id parameter
        $user = AuthenticatedUser::find($id);

        if (!$user) {
            // Handle the case where the user is not found (e.g., show an error message)
            return abort(404);
        }

        // Return the edit profile form view with the user's data
        return view('pages.editprofile', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Retrieve the user based on the $id parameter
        $user = AuthenticatedUser::find($id);

        if (!$user) {
            // Handle the case where the user is not found (e.g., show an error message)
            return abort(404);
        }

        // Validate and update the user's information
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:authenticated_user,username,'.$id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:authenticated_user,email,'.$id,
            'password' => 'nullable|string|min:8', // Allow the password to be nullable if not changed
        ]);

        // Hash the password if provided
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            // If the password is not provided, remove it from the data array
            unset($validatedData['password']);
        }

        // Update the user's information
        $user->update($validatedData);

        // Redirect the user to their profile page or another relevant page
        return redirect()->route('show', ['id' => $id])->with('success', 'Profile updated successfully');
    }

    public function balance($id)
    {
        $user = AuthenticatedUser::find($id);
    
        if (!$user) {
            return abort(404);
        }
    
        return view('pages.balance', compact('user'));
    }
    
    public function addFunds(Request $request, $id)
    {
        $user = AuthenticatedUser::find($id);
    
        if (!$user) {
            return abort(404);
        }
    
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
    
        $user->balance += $request->input('amount');
        $user->save();
    
        return redirect()->route('show', ['id' => $id])->with('success', 'Funds added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuthenticatedUser $authenticatedUser)
    {
        //
    }

    public function showAboutUs()
    {
        
      return view('pages.aboutUs');
      
    }

    public function showFAQ()
    {
        
      return view('pages.faq');
      
    }
    
    public function showFeatures()
    {
        return view('pages.features');
    }

    public function showContacts()
    {
        return view('pages.contacts');
    }
    
}
