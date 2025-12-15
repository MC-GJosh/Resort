<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index()
    {
        $rooms = Room::active()->get();

        return response()->json($rooms);
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:rooms,slug',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'size' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|string',
            'image_class' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Room created successfully',
            'room' => $room,
        ], 201);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return response()->json($room);
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|unique:rooms,slug,' . $room->id,
            'price' => 'sometimes|numeric|min:0',
            'capacity' => 'sometimes|integer|min:1',
            'size' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|string',
            'image_class' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $room->update($validated);

        return response()->json([
            'message' => 'Room updated successfully',
            'room' => $room,
        ]);
    }

    /**
     * Remove the specified room.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully',
        ]);
    }

    /**
     * Check room availability for a date range.
     */
    public function availability(Request $request, Room $room)
    {
        $validated = $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $isAvailable = $room->isAvailable($validated['check_in'], $validated['check_out']);
        $totalPrice = $room->calculatePrice($validated['check_in'], $validated['check_out']);

        return response()->json([
            'room' => $room,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'available' => $isAvailable,
            'total_price' => $totalPrice,
        ]);
    }
}
