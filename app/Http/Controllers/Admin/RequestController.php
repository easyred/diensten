<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display a listing of service requests
     */
    public function index()
    {
        $requests = WaRequest::with(['customer', 'selectedPlumber'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.requests.index', compact('requests'));
    }

    /**
     * Display the specified request
     */
    public function show(WaRequest $request)
    {
        $request->load(['customer', 'selectedPlumber']);
        return view('admin.requests.show', compact('request'));
    }

    /**
     * Update the status of a request
     */
    public function updateStatus(Request $request, WaRequest $waRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:broadcasting,active,in_progress,completed,cancelled'],
        ]);

        $waRequest->update(['status' => $validated['status']]);

        if ($validated['status'] === 'completed') {
            $waRequest->update(['completed_at' => now()]);
        }

        return redirect()->back()->with('success', 'Request status updated successfully.');
    }
}

