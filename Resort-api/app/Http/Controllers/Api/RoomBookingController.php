<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Http\Request;

class RoomBookingController extends Controller
{
    /**
     * Display a listing of the user's room bookings.
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->roomBookings()
            ->with('room')
            ->orderBy('check_in', 'desc')
            ->get();

        return response()->json($bookings);
    }

    /**
     * Store a newly created room booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'guest_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'payment_method' => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'special_requests' => 'nullable|string',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Check if room is available
        if (!$room->isAvailable($validated['check_in'], $validated['check_out'])) {
            return response()->json([
                'message' => 'Room is not available for the selected dates',
            ], 409);
        }

        // Validate guest count against room capacity
        if ($validated['guests'] > $room->capacity) {
            return response()->json([
                'message' => 'Number of guests exceeds room capacity',
                'max_capacity' => $room->capacity,
            ], 422);
        }

        // Calculate total price
        $totalPrice = $room->calculatePrice($validated['check_in'], $validated['check_out']);

        $booking = RoomBooking::create([
            'user_id' => $request->user()->id,
            'room_id' => $validated['room_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'guests' => $validated['guests'],
            'guest_name' => $validated['guest_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'payment_method' => $validated['payment_method'] ?? 'GCash',
            'reference_number' => $validated['reference_number'] ?? null,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'confirmed',
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Room booking created successfully',
            'booking' => $booking->load('room'),
        ], 201);
    }

    /**
     * Display the specified room booking.
     */
    public function show(Request $request, RoomBooking $roomBooking)
    {
        // Ensure user owns this booking or is admin
        if ($roomBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($roomBooking->load('room'));
    }

    /**
     * Update the specified room booking.
     */
    public function update(Request $request, RoomBooking $roomBooking)
    {
        // Ensure user owns this booking or is admin
        if ($roomBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,cancelled,checked_in,checked_out',
            'guest_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string',
        ]);

        $roomBooking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $roomBooking,
        ]);
    }

    /**
     * Remove the specified room booking (cancel).
     */
    public function destroy(Request $request, RoomBooking $roomBooking)
    {
        // Ensure user owns this booking or is admin
        if ($roomBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $roomBooking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
        ]);
    }
}
