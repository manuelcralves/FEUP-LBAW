<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthenticatedUser;

class ProfileController extends Controller
{
    public function show($id)
    {
        // Retrieve the user based on the $id parameter
        $user = AuthenticatedUser::find($id);
    
        if (!$user) {
            return abort(403);
        }
    
        // Pass the user data to the profile view
        return view('pages.profile', compact('user'));
    }    
}
