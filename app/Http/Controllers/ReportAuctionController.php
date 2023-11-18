<?php

namespace App\Http\Controllers;

use App\Models\ReportAuction;
use Illuminate\Http\Request;

class ReportAuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reportAuctions = ReportAuction::all();
        return view('report_auctions.index', compact('reportAuctions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report_auctions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Add validation rules for report_auction fields
        ]);

        ReportAuction::create($validatedData);

        return redirect()->route('report_auctions.index')
            ->with('success', 'ReportAuction created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportAuction $reportAuction)
    {
        return view('report_auctions.show', compact('reportAuction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportAuction $reportAuction)
    {
        return view('report_auctions.edit', compact('reportAuction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportAuction $reportAuction)
    {
        $validatedData = $request->validate([
            // Add validation rules for report_auction fields
        ]);

        $reportAuction->update($validatedData);

        return redirect()->route('report_auctions.index')
            ->with('success', 'ReportAuction updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportAuction $reportAuction)
    {
        $reportAuction->delete();

        return redirect()->route('report_auctions.index')
            ->with('success', 'ReportAuction deleted successfully');
    }
}
