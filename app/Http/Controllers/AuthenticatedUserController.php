<?php

namespace App\Http\Controllers;

use App\Models\AuthenticatedUser;
use Illuminate\Http\Request;

class AuthenticatedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authenticatedUsers = AuthenticatedUser::all();
        return view('authenticated_users.index', compact('authenticatedUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('authenticated_users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|unique:authenticated_users',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:authenticated_users',
            'password' => 'required',
            'rating' => 'numeric|nullable',
            'picture' => 'string|nullable',
            'balance' => 'numeric|nullable',
            'is_blocked' => 'boolean',
            'role' => 'required|in:USER,ADMIN', // Assuming ROLES is an enum with values USER and ADMIN
            // Add validation rules for other fields
        ]);

        AuthenticatedUser::create($validatedData);

        return redirect()->route('authenticated_users.index')
            ->with('success', 'Authenticated user created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthenticatedUser $authenticatedUser)
    {
        return view('authenticated_users.show', compact('authenticatedUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuthenticatedUser $authenticatedUser)
    {
        return view('authenticated_users.edit', compact('authenticatedUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AuthenticatedUser $authenticatedUser)
    {
        $validatedData = $request->validate([
            'username' => 'required|unique:authenticated_users,username,' . $authenticatedUser->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:authenticated_users,email,' . $authenticatedUser->id,
            'rating' => 'numeric|nullable',
            'picture' => 'string|nullable',
            'balance' => 'numeric|nullable',
            'is_blocked' => 'boolean',
            'role' => 'required|in:USER,ADMIN',
            // Add validation rules for other fields
        ]);

        $authenticatedUser->update($validatedData);

        return redirect()->route('authenticated_users.index')
            ->with('success', 'Authenticated user updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuthenticatedUser $authenticatedUser)
    {
        $authenticatedUser->delete();

        return redirect()->route('authenticated_users.index')
            ->with('success', 'Authenticated user deleted successfully');
    }
}
