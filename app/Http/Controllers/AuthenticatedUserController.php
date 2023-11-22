<?php

namespace App\Http\Controllers;

use App\Models\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Policies\AuthenticatedUserPolicy;

class AuthenticatedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.home');
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
            $user->role = 'ADMIN';
            $user->save();
    
            return back()->with('success', 'User promoted to ADMIN successfully');
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
      

    public function all(Request $request, $pageNr)
    {
        $perPage = 5; // Number of users per page
        $query = $request->input('query'); // Get the search query from the request
    
        // Query users based on the search criteria
        $users = AuthenticatedUser::where('username', 'LIKE', "%$query%")
            ->orWhere('first_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->paginate($perPage, ['*'], 'page', $pageNr);
    
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
}
