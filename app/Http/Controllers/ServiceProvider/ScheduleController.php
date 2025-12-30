<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceProviderSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Show the schedule management page
     */
    public function index()
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);
        
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
        } else {
            // Ensure schedule has all days set to open24 by default if not already set
            $scheduleData = $schedule->schedule_data ?? [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $needsUpdate = false;
            
            foreach ($days as $day) {
                if (!isset($scheduleData[$day]) || ($scheduleData[$day]['mode'] ?? '') !== 'open24') {
                    $scheduleData[$day] = [
                        'mode' => 'open24',
                        'split' => $scheduleData[$day]['split'] ?? ['o1' => '09:00', 'c1' => '12:00', 'o2' => '13:30', 'c2' => '19:00'],
                        'full' => $scheduleData[$day]['full'] ?? ['o' => '09:00', 'c' => '19:00'],
                    ];
                    $needsUpdate = true;
                }
            }
            
            if ($needsUpdate) {
                $schedule->schedule_data = $scheduleData;
                $schedule->last_updated = now();
                $schedule->save();
            }
        }
        
        return view('service-provider.schedule.index', compact('schedule'));
    }

    /**
     * Update the schedule
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        $request->validate([
            'schedule_data' => 'required|array',
            'holidays' => 'nullable|array',
            'holidays.*' => 'date',
            'vacations' => 'nullable|array',
            'vacations.*.from' => 'required_with:vacations.*.to|date',
            'vacations.*.to' => 'required_with:vacations.*.from|date',
            'vacations.*.note' => 'nullable|string'
        ]);

        $schedule = ServiceProviderSchedule::where('user_id', $user->id)->first();
        
        if (!$schedule) {
            $schedule = new ServiceProviderSchedule(['user_id' => $user->id]);
        }
        
        $schedule->fill([
            'schedule_data' => $request->schedule_data,
            'holidays' => $request->holidays ?? [],
            'vacations' => $request->vacations ?? [],
            'last_updated' => now()
        ]);
        
        $schedule->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule
        ]);
    }

    /**
     * Get schedule data for API
     */
    public function getSchedule()
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);
        
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
        } else {
            // Ensure schedule has all days set to open24 by default if not already set
            $scheduleData = $schedule->schedule_data ?? [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $needsUpdate = false;
            
            foreach ($days as $day) {
                if (!isset($scheduleData[$day]) || ($scheduleData[$day]['mode'] ?? '') !== 'open24') {
                    $scheduleData[$day] = [
                        'mode' => 'open24',
                        'split' => $scheduleData[$day]['split'] ?? ['o1' => '09:00', 'c1' => '12:00', 'o2' => '13:30', 'c2' => '19:00'],
                        'full' => $scheduleData[$day]['full'] ?? ['o' => '09:00', 'c' => '19:00'],
                    ];
                    $needsUpdate = true;
                }
            }
            
            if ($needsUpdate) {
                $schedule->schedule_data = $scheduleData;
                $schedule->last_updated = now();
                $schedule->save();
            }
        }
        
        return response()->json($schedule);
    }

    /**
     * Check availability at specific time
     */
    public function checkAvailability(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        $request->validate([
            'datetime' => 'required|date'
        ]);
        
        $schedule = ServiceProviderSchedule::where('user_id', $user->id)->first();
        
        if (!$schedule) {
            return response()->json(['available' => false, 'reason' => 'No schedule set']);
        }
        
        $dateTime = Carbon::parse($request->datetime);
        $isAvailable = $schedule->isAvailableAt($dateTime);
        
        return response()->json([
            'available' => $isAvailable,
            'datetime' => $dateTime->toISOString(),
            'next_available' => $schedule->getNextAvailableTime($dateTime)?->toISOString()
        ]);
    }
}
