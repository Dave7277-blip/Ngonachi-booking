<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_name',
        'client_email',
        'client_phone',
        'package_id',
        'event_type',
        'event_date',
        'event_location',
        'notes',
        'status',
        'admin_notes',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'event_date'   => 'date',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // ── Static helpers ─────────────────────────────────────
    /**
     * Generate a unique sequential reference like BK001, BK002 …
     * Uses a DB lock to be race-condition safe.
     */
    public static function generateReference(): string
    {
        $last = static::lockForUpdate()->latest('id')->first();
        $next = $last ? ((int) substr($last->reference, 2)) + 1 : 1;
        return 'BK' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    // ── Scopes ─────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeUpcoming($query)
    {
        return $query
            ->where('event_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('event_date', 'asc');
    }
}