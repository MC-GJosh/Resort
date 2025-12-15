<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    /**
     * Display a listing of the courts.
     */
    public function index()
    {
        $courts = Court::active()->get();

        return response()->json($courts);
    }

    /**
     * Store a newly created court.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'surface' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $court = Court::create($validated);

        return response()->json([
            'message' => 'Court created successfully',
            'court' => $court,
        ], 201);
    }

    /**
     * Display the specified court.
     */
    public function show(Court $court)
    {
        return response()->json($court);
    }

    /**
     * Update the specified court.
     */
    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'rate' => 'sometimes|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'surface' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $court->update($validated);

        return response()->json([
            'message' => 'Court updated successfully',
            'court' => $court,
        ]);
    }

    /**
     * Remove the specified court.
     */
    public function destroy(Court $court)
    {
        $court->delete();

        return response()->json([
            'message' => 'Court deleted successfully',
        ]);
    }

    /**
     * Get availability for a specific court on a date.
     */
    public function availability(Request $request, Court $court)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $timeSlots = [
            '6:00 AM - 7:00 AM', '7:00 AM - 8:00 AM', '8:00 AM - 9:00 AM',
            '9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM',
            '12:00 PM - 1:00 PM', '1:00 PM - 2:00 PM', '2:00 PM - 3:00 PM',
            '3:00 PM - 4:00 PM', '4:00 PM - 5:00 PM', '5:00 PM - 6:00 PM',
        ];

        $bookedSlots = $court->getBookedSlots($validated['date']);

        $availability = collect($timeSlots)->map(function ($slot) use ($bookedSlots) {
            return [
                'time_slot' => $slot,
                'available' => !in_array($slot, $bookedSlots),
            ];
        });

        return response()->json([
            'court' => $court,
            'date' => $validated['date'],
            'slots' => $availability,
        ]);
    }
}
