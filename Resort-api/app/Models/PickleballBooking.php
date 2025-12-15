<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickleballBooking extends Model
{
    protected $fillable = [
        'user_id',
        'court_id',
        'date',
        'time_slot',
        'customer_name',
        'phone',
        'payment_method',
        'reference_number',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the court for the booking.
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * Scope for confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for a specific date.
     */
    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }
}
