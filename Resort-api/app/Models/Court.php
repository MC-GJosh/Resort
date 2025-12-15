<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'location',
        'surface',
        'description',
        'image',
        'features',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the bookings for the court.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(PickleballBooking::class);
    }

    /**
     * Scope to get only active courts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a specific time slot is available on a given date.
     */
    public function isSlotAvailable(string $date, string $timeSlot): bool
    {
        return !$this->bookings()
            ->whereDate('date', $date)
            ->where('time_slot', $timeSlot)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Get booked slots for a specific date.
     */
    public function getBookedSlots(string $date): array
    {
        return $this->bookings()
            ->whereDate('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('time_slot')
            ->toArray();
    }
}
