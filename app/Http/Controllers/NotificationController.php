<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notification::all();
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required',
            'type' => 'required|in:INFO,WARNING,ERROR',
            'read' => 'boolean',
            // Add validation rules for other fields
        ]);

        Notification::create($validatedData);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        return view('notifications.edit', compact('notification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $validatedData = $request->validate([
            'message' => 'required',
            'type' => 'required|in:INFO,WARNING,ERROR',
            'read' => 'boolean',
            // Add validation rules for other fields
        ]);

        $notification->update($validatedData);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully');
    }
}
