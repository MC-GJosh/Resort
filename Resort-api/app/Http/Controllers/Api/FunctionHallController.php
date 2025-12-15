<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FunctionHall;
use Illuminate\Http\Request;

class FunctionHallController extends Controller
{
    /**
     * Display a listing of the function halls.
     */
    public function index()
    {
        $halls = FunctionHall::active()->get();

        return response()->json($halls);
    }

    /**
     * Store a newly created function hall.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:function_halls,slug',
            'price_per_4hrs' => 'required|numeric|min:0',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1|gte:min_capacity',
            'size' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|string',
            'image_class' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $hall = FunctionHall::create($validated);

        return response()->json([
            'message' => 'Function hall created successfully',
            'hall' => $hall,
        ], 201);
    }

    /**
     * Display the specified function hall.
     */
    public function show(FunctionHall $functionHall)
    {
        return response()->json($functionHall);
    }

    /**
     * Update the specified function hall.
     */
    public function update(Request $request, FunctionHall $functionHall)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|unique:function_halls,slug,' . $functionHall->id,
            'price_per_4hrs' => 'sometimes|numeric|min:0',
            'min_capacity' => 'sometimes|integer|min:1',
            'max_capacity' => 'sometimes|integer|min:1',
            'size' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|string',
            'image_class' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $functionHall->update($validated);

        return response()->json([
            'message' => 'Function hall updated successfully',
            'hall' => $functionHall,
        ]);
    }

    /**
     * Remove the specified function hall.
     */
    public function destroy(FunctionHall $functionHall)
    {
        $functionHall->delete();

        return response()->json([
            'message' => 'Function hall deleted successfully',
        ]);
    }

    /**
     * Check function hall availability for a specific date.
     */
    public function availability(Request $request, FunctionHall $functionHall)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
        ]);

        $isAvailable = $functionHall->isAvailable($validated['event_date']);

        return response()->json([
            'hall' => $functionHall,
            'event_date' => $validated['event_date'],
            'available' => $isAvailable,
        ]);
    }
}
