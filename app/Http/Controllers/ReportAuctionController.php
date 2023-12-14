<?php

namespace App\Http\Controllers;

use App\Models\ReportAuction;
use Illuminate\Http\Request;

use App\Models\AuthenticatedUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Policies\AuthenticatedUserPolicy;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;

class ReportAuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.report');
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
                'reason' => 'required|string|max:1023',
            ]);
            

            $report = new ReportAuction($validatedData);
            $report->user= $user->id;
            //$report->auction= $auction->id;
            $report->save();
            
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

   

    /**
     * Display the specified resource.
     */
    public function show(ReportAuction $reportAuction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportAuction $reportAuction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportAuction $reportAuction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportAuction $reportAuction)
    {
        //
    }
}
