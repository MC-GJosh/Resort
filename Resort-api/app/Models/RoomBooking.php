<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBooking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'guest_name',
        'email',
        'phone',
        'payment_method',
        'reference_number',
        'special_requests',
        'status',
        'total_price',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'guests' => 'integer',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room for the booking.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the number of nights.
     */
    public function getNightsAttribute(): int
    {
        return $this->check_out->diffInDays($this->check_in);
    }

    /**
     * Scope for confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for active bookings (not cancelled or checked out).
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'checked_out']);
    }
}
