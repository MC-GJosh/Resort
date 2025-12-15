<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FunctionHall;
use App\Models\HallBooking;
use Illuminate\Http\Request;

class HallBookingController extends Controller
{
    /**
     * Display a listing of the user's hall bookings.
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->hallBookings()
            ->with('functionHall')
            ->orderBy('event_date', 'desc')
            ->get();

        return response()->json($bookings);
    }

    /**
     * Store a newly created hall booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'function_hall_id' => 'required|exists:function_halls,id',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'guest_count' => 'required|integer|min:1',
            'catering_package' => 'nullable|string|max:100',
            'main_dish' => 'nullable|string|max:100',
            'appetizer' => 'nullable|string|max:100',
            'drink' => 'nullable|string|max:100',
            'avail_mini_bar' => 'boolean',
            'payment_method' => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $hall = FunctionHall::findOrFail($validated['function_hall_id']);

        // Check if hall is available for the date
        if (!$hall->isAvailable($validated['event_date'])) {
            return response()->json([
                'message' => 'Function hall is not available for the selected date',
            ], 409);
        }

        // Validate guest count against hall capacity
        if ($validated['guest_count'] < $hall->min_capacity || $validated['guest_count'] > $hall->max_capacity) {
            return response()->json([
                'message' => 'Guest count must be between ' . $hall->min_capacity . ' and ' . $hall->max_capacity,
                'min_capacity' => $hall->min_capacity,
                'max_capacity' => $hall->max_capacity,
            ], 422);
        }

        // Calculate total price (base price + catering extras if applicable)
        $totalPrice = $hall->price_per_4hrs;

        // Add mini bar fee if selected
        if ($validated['avail_mini_bar'] ?? false) {
            $totalPrice += 5000; // Example mini bar surcharge
        }

        $booking = HallBooking::create([
            'user_id' => $request->user()->id,
            'function_hall_id' => $validated['function_hall_id'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'event_date' => $validated['event_date'],
            'guest_count' => $validated['guest_count'],
            'catering_package' => $validated['catering_package'] ?? null,
            'main_dish' => $validated['main_dish'] ?? null,
            'appetizer' => $validated['appetizer'] ?? null,
            'drink' => $validated['drink'] ?? null,
            'avail_mini_bar' => $validated['avail_mini_bar'] ?? false,
            'payment_method' => $validated['payment_method'] ?? 'GCash',
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Hall booking created successfully',
            'booking' => $booking->load('functionHall'),
        ], 201);
    }

    /**
     * Display the specified hall booking.
     */
    public function show(Request $request, HallBooking $hallBooking)
    {
        // Ensure user owns this booking or is admin
        if ($hallBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($hallBooking->load('functionHall'));
    }

    /**
     * Update the specified hall booking.
     */
    public function update(Request $request, HallBooking $hallBooking)
    {
        // Ensure user owns this booking or is admin
        if ($hallBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed',
            'full_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $hallBooking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $hallBooking,
        ]);
    }

    /**
     * Remove the specified hall booking (cancel).
     */
    public function destroy(Request $request, HallBooking $hallBooking)
    {
        // Ensure user owns this booking or is admin
        if ($hallBooking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $hallBooking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
        ]);
    }
    public function confirm(Request $request, $id)
    {
        $booking = HallBooking::findOrFail($id);
        $booking->status = 'confirmed';
        $booking->save();

        if ($booking->email) {
            // Adapt booking for the common mailable view
            // The view expects: customer_name, date, time_slot, reference_number
            // HallBooking has: full_name, event_date, (time is usually full day/standard), reference_number
            $bookingAdapter = clone $booking;
            $bookingAdapter->customer_name = $booking->full_name;
            $bookingAdapter->date = $booking->event_date;
            $bookingAdapter->time_slot = 'See details';

            \Illuminate\Support\Facades\Mail::to($booking->email)->send(new \App\Mail\BookingConfirmed($bookingAdapter));
        }

        return response()->json(['message' => 'Booking confirmed and email sent.']);
    }

    public function cancel(Request $request, $id)
    {
        $booking = HallBooking::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();

        if ($booking->email) {
            $bookingAdapter = clone $booking;
            $bookingAdapter->customer_name = $booking->full_name;
            $bookingAdapter->date = $booking->event_date;
            $bookingAdapter->time_slot = 'N/A';

            \Illuminate\Support\Facades\Mail::to($booking->email)->send(new \App\Mail\BookingCancelled($bookingAdapter));
        }

        return response()->json(['message' => 'Booking cancelled and email sent.']);
    }
}
