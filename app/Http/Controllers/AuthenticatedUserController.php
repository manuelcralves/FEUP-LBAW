<?php

namespace App\Http\Controllers;

use App\Models\AuthenticatedUser;
use Illuminate\Http\Request;

class AuthenticatedUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('authenticated_users.show')->with('authenticated_users', Auth::AuthenticatedUser());
        $this->AuthenticatedUserRepository->pushCriteria(new RequestCriteria($request));
        $authenticatedUsers = $this->AuthenticatedUserRepository->all();

        return view('authenticated_users.index')
        with -> ('authenticated_users', $authenticatedUsers);
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
            'role' => 'required|in:USER,ADMIN',
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
        $authenticatedUser = $this->AuthenticatedUserRepository->findWithoutFail($id);

        if (empty($item)) {
            Flash::error('Authenticated user not found');

            return redirect(route('authenticated_users.index'));
        }

        return view('authenticated_users.show')->with('authenticated_users', $authenticatedUser);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuthenticatedUser $authenticatedUser)
    {
        $authenticatedUser = $this->AuthenticatedUserRepository->findWithoutFail($authenticatedUser);

        if (empty($authenticatedUser)) {
            Flash::error('Authenticated user not found');

            return redirect(route('authenticated_users.index'));
        }

        return view('authenticated_users.edit')->with('authenticated_users', $authenticatedUser);
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
        $authenticatedUser = $this->AuthenticatedUserRepository->findWithoutFail($authenticatedUser);

        if (empty($authenticatedUser)) {
            Flash::error('User not found');

            return redirect(route('authenticated_users.index'));
        }

        $this->userRepository->delete($id);

        Flash::success('User deleted successfully.');

        return redirect(route('authenticated_users.index'));

    }
}
