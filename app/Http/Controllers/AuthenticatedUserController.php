<?php

namespace App\Http\Controllers;

use App\Models\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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


    /**
     * Display the specified resource.
     */
    public function show(AuthenticatedUser $authenticatedUser)
    {
        // Retrieve the authenticated user from the session
        $user = auth()->user();
    
        if (!$user) {
            return abort(403);
        }
    
        // Pass the user data to the profile view
        return view('pages.profile', compact('user'));
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
