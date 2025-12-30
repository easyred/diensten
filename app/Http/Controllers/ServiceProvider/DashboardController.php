<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WaRequest;
use App\Models\ServiceProviderSchedule;

class DashboardController extends Controller
{
    /**
     * Show the service provider dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ensure user is a service provider
        if (!in_array($user->role, ['plumber', 'gardener'])) {
            abort(403, 'Access denied. Service providers only.');
        }

        // Get statistics
        $stats = [
            'total_requests' => WaRequest::where('selected_plumber_id', $user->id)->count(),
            'active_requests' => WaRequest::where('selected_plumber_id', $user->id)
                ->whereIn('status', ['active', 'in_progress'])
                ->count(),
            'completed_requests' => WaRequest::where('selected_plumber_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'pending_requests' => WaRequest::where('status', 'broadcasting')
                ->whereHas('category', function($q) use ($user) {
                    $q->whereHas('users', function($uq) use ($user) {
                        $uq->where('users.id', $user->id);
                    });
                })
                ->count(),
            // Review statistics
            'total_reviews' => \App\Models\Rating::where('service_provider_id', $user->id)->count(),
            'average_rating' => \App\Models\Rating::where('service_provider_id', $user->id)->avg('rating'),
            'total_services' => WaRequest::where('selected_plumber_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];

        // Get recent requests
        $recentRequests = WaRequest::where('selected_plumber_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending requests (broadcasting to this provider's categories)
        $pendingRequests = WaRequest::where('status', 'broadcasting')
            ->whereHas('category', function($q) use ($user) {
                $q->whereHas('users', function($uq) use ($user) {
                    $uq->where('users.id', $user->id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Ensure schedule exists (create with 24-hour defaults if not exists)
        $schedule = ServiceProviderSchedule::where('user_id', $user->id)->first();
        if (!$schedule) {
            $schedule = ServiceProviderSchedule::create([
                'user_id' => $user->id,
                'timezone' => 'Europe/Brussels',
                'schedule_data' => ServiceProviderSchedule::getDefaultSchedule(),
                'holidays' => [],
                'vacations' => [],
                'last_updated' => now()
            ]);
        }

        return view('service-provider.dashboard', compact('stats', 'recentRequests', 'pendingRequests', 'schedule'));
    }
}
