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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuthenticatedUser $authenticatedUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AuthenticatedUser $authenticatedUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuthenticatedUser $authenticatedUser)
    {
        //
    }
}
