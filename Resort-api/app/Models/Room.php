<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'capacity',
        'size',
        'description',
        'amenities',
        'image',
        'image_class',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot function to auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->slug)) {
                $room->slug = Str::slug($room->name);
            }
        });
    }

    /**
     * Get the bookings for the room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class);
    }

    /**
     * Scope to get only active rooms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the room is available for a date range.
     */
    public function isAvailable(string $checkIn, string $checkOut): bool
    {
        return !$this->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                          ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }

    /**
     * Calculate the total price for a stay.
     */
    public function calculatePrice(string $checkIn, string $checkOut): float
    {
        $start = new \DateTime($checkIn);
        $end = new \DateTime($checkOut);
        $nights = $end->diff($start)->days;
        
        return $nights > 0 ? $this->price * $nights : $this->price;
    }
}
