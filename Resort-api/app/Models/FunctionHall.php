<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FunctionHall extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price_per_4hrs',
        'min_capacity',
        'max_capacity',
        'size',
        'description',
        'amenities',
        'image',
        'image_class',
        'badge',
        'is_premium',
        'is_active',
    ];

    protected $casts = [
        'price_per_4hrs' => 'decimal:2',
        'min_capacity' => 'integer',
        'max_capacity' => 'integer',
        'amenities' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot function to auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hall) {
            if (empty($hall->slug)) {
                $hall->slug = Str::slug($hall->name);
            }
        });
    }

    /**
     * Get the bookings for the hall.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(HallBooking::class);
    }

    /**
     * Scope to get only active halls.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the hall is available for a specific date.
     */
    public function isAvailable(string $eventDate): bool
    {
        return !$this->bookings()
            ->where('event_date', $eventDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Get the capacity range as string.
     */
    public function getCapacityRangeAttribute(): string
    {
        return "{$this->min_capacity} - {$this->max_capacity} Guests";
    }
}
