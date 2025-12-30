<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeleRecord;
use Illuminate\Http\Request;

class TeleController extends Controller
{
    /**
     * Display a listing of tele records
     */
    public function index()
    {
        $teleRecords = TeleRecord::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.tele.index', compact('teleRecords'));
    }

    /**
     * Show the form for creating a new tele record
     */
    public function create()
    {
        return view('admin.tele.create');
    }

    /**
     * Store a newly created tele record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'contacted_date' => 'nullable|date',
            'status' => 'required|in:Sent,Active,Called,Interested,Paid',
            'message' => 'nullable|string',
        ]);

        TeleRecord::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Tele record created successfully.']);
        }

        return redirect()->route('admin.tele.index')
            ->with('success', 'Tele record created successfully.');
    }

    /**
     * Display the specified tele record
     */
    public function show(TeleRecord $tele)
    {
        $tele->load('user');
        return response()->json($tele);
    }

    /**
     * Show the form for editing the specified tele record
     */
    public function edit(TeleRecord $tele)
    {
        return view('admin.tele.edit', compact('tele'));
    }

    /**
     * Update the specified tele record
     */
    public function update(Request $request, TeleRecord $tele)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'contacted_date' => 'nullable|date',
            'status' => 'required|in:Sent,Active,Called,Interested,Paid',
            'message' => 'nullable|string',
        ]);

        $tele->update($validated);

        return redirect()->route('admin.tele.index')
            ->with('success', 'Tele record updated successfully.');
    }

    /**
     * Remove the specified tele record
     */
    public function destroy(TeleRecord $tele)
    {
        $tele->delete();

        return redirect()->route('admin.tele.index')
            ->with('success', 'Tele record deleted successfully.');
    }
}

