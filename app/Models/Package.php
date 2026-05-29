<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'currency',
        'description',
        'features',
        'hours_coverage',
        'photographers_count',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    /**
     * 'decimal:2' is NOT a valid Laravel cast key — it silently fails.
     * Use 'float' for numeric operations and cast manually when displaying.
     * 'array' correctly JSON-encodes/decodes the features column.
     */
    protected $casts = [
        'features'            => 'array',
        'is_featured'         => 'boolean',
        'is_active'           => 'boolean',
        'price'               => 'float',
        'hours_coverage'      => 'integer',
        'photographers_count' => 'integer',
        'sort_order'          => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    /**
     * Returns a formatted price string e.g. "TZS 2,500,000"
     * Compatible with Laravel 8, 9, 10, and 11.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format((float) $this->price, 0);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}