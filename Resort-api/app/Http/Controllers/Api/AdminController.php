<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\FunctionHall;
use App\Models\HallBooking;
use App\Models\PickleballBooking;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function dashboard()
    {
        // Count stats
        $stats = [
            'users' => User::count(),
            'courts' => Court::count(),
            'rooms' => Room::count(),
            'function_halls' => FunctionHall::count(),
        ];

        // Booking counts by status
        $pickleballBookings = PickleballBooking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $roomBookings = RoomBooking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $hallBookings = HallBooking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Revenue calculations
        $revenue = [
            'pickleball' => PickleballBooking::whereIn('status', ['confirmed', 'completed'])
                ->join('courts', 'pickleball_bookings.court_id', '=', 'courts.id')
                ->sum('courts.rate'),
            'rooms' => RoomBooking::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->sum('total_price'),
            'halls' => HallBooking::whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
        ];
        $revenue['total'] = $revenue['pickleball'] + $revenue['rooms'] + $revenue['halls'];

        // Recent bookings
        $recentPickleball = PickleballBooking::with('court')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentRooms = RoomBooking::with('room')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentHalls = HallBooking::with('functionHall')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Today's bookings
        $today = now()->toDateString();
        $todayStats = [
            'pickleball' => PickleballBooking::whereDate('date', $today)->count(),
            'room_checkins' => RoomBooking::whereDate('check_in', $today)->count(),
            'hall_events' => HallBooking::whereDate('event_date', $today)->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'bookings' => [
                'pickleball' => $pickleballBookings,
                'rooms' => $roomBookings,
                'halls' => $hallBookings,
            ],
            'revenue' => $revenue,
            'today' => $todayStats,
            'recent' => [
                'pickleball' => $recentPickleball,
                'rooms' => $recentRooms,
                'halls' => $recentHalls,
            ],
        ]);
    }

    /**
     * Get all pickleball bookings (admin view).
     */
    public function pickleballBookings(Request $request)
    {
        $query = PickleballBooking::with(['court', 'user']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->has('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        $bookings = $query->orderBy('date', 'desc')
            ->orderBy('time_slot', 'asc')
            ->paginate(20);

        return response()->json($bookings);
    }

    /**
     * Get all room bookings (admin view).
     */
    public function roomBookings(Request $request)
    {
        $query = RoomBooking::with(['room', 'user']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $bookings = $query->orderBy('check_in', 'desc')
            ->paginate(20);

        return response()->json($bookings);
    }

    /**
     * Get all hall bookings (admin view).
     */
    public function hallBookings(Request $request)
    {
        $query = HallBooking::with(['functionHall', 'user']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('function_hall_id')) {
            $query->where('function_hall_id', $request->function_hall_id);
        }

        $bookings = $query->orderBy('event_date', 'desc')
            ->paginate(20);

        return response()->json($bookings);
    }

    /**
     * Update booking status (any type).
     */
    public function updatePickleballBooking(Request $request, PickleballBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking status updated',
            'booking' => $booking->load('court'),
        ]);
    }

    public function updateRoomBooking(Request $request, RoomBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,checked_in,checked_out',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking status updated',
            'booking' => $booking->load('room'),
        ]);
    }

    public function updateHallBooking(Request $request, HallBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking status updated',
            'booking' => $booking->load('functionHall'),
        ]);
    }

    /**
     * Get all users (admin view).
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($users);
    }
}
