<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\PickleballBooking;
use Illuminate\Http\Request;

class PickleballBookingController extends Controller
{
    /**
     * Display a listing of the user's bookings.
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->pickleballBookings()
            ->with('court')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get all bookings (for availability display).
     */
    public function all(Request $request)
    {
        $validated = $request->validate([
            'court_id' => 'nullable|exists:courts,id',
            'date' => 'nullable|date',
        ]);

        $query = PickleballBooking::with('court')
            ->whereIn('status', ['pending', 'confirmed']);

        if (isset($validated['court_id'])) {
            $query->where('court_id', $validated['court_id']);
        }

        if (isset($validated['date'])) {
            $query->where('date', $validated['date']);
        }

        $bookings = $query->get();

        return response()->json($bookings);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slots' => 'required|array|min:1',
            'time_slots.*' => 'string',
            'customer_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'payment_method' => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $court = Court::findOrFail($validated['court_id']);

        // Check availability for all slots
        $unavailableSlots = [];
        foreach ($validated['time_slots'] as $slot) {
            if (!$court->isSlotAvailable($validated['date'], $slot)) {
                $unavailableSlots[] = $slot;
            }
        }

        if (!empty($unavailableSlots)) {
            return response()->json([
                'message' => 'Some time slots are already booked',
                'unavailable_slots' => $unavailableSlots,
            ], 409);
        }

        // Create bookings for each slot
        $bookings = [];
        foreach ($validated['time_slots'] as $slot) {
            $bookings[] = PickleballBooking::create([
                'user_id' => $request->user()->id,
                'court_id' => $validated['court_id'],
                'date' => $validated['date'],
                'time_slot' => $slot,
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'] ?? null,
                'payment_method' => $validated['payment_method'] ?? 'GCash',
                'reference_number' => $validated['reference_number'] ?? null,
                'status' => 'confirmed',
            ]);
        }

        return response()->json([
            'message' => 'Booking created successfully',
            'bookings' => $bookings,
            'total_cost' => $court->rate * count($validated['time_slots']),
        ], 201);
    }

    /**
     * Display the specified booking.
     */
    public function show(Request $request, PickleballBooking $pickleballBooking)
    {
        // Ensure user owns this booking or is admin
        if ($pickleballBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($pickleballBooking->load('court'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, PickleballBooking $pickleballBooking)
    {
        // Ensure user owns this booking or is admin
        if ($pickleballBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed',
            'customer_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $pickleballBooking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $pickleballBooking,
        ]);
    }

    /**
     * Remove the specified booking (cancel).
     */
    public function destroy(Request $request, PickleballBooking $pickleballBooking)
    {
        // Ensure user owns this booking or is admin
        if ($pickleballBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pickleballBooking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
        ]);
    }
}
