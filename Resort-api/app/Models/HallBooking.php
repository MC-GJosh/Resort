<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallBooking extends Model
{
    protected $fillable = [
        'user_id',
        'function_hall_id',
        'full_name',
        'phone',
        'email',
        'address',
        'event_date',
        'guest_count',
        'catering_package',
        'main_dish',
        'appetizer',
        'drink',
        'avail_mini_bar',
        'payment_method',
        'reference_number',
        'notes',
        'status',
        'total_price',
    ];

    protected $casts = [
        'event_date' => 'date',
        'guest_count' => 'integer',
        'avail_mini_bar' => 'boolean',
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
     * Get the function hall for the booking.
     */
    public function functionHall(): BelongsTo
    {
        return $this->belongsTo(FunctionHall::class);
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
     * Check if catering is included.
     */
    public function hasCatering(): bool
    {
        return !empty($this->catering_package);
    }
}
