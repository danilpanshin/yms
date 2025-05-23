<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateBooking extends Model
{
    protected $fillable = [
        'gate_id', 'booking_date', 'start_time', 'end_time',
        'pallets_count', 'purpose', 'user_id', 'is_internal'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public static function calculateDuration(int $palletsCount): int
    {
        // Каждые 33 паллеты добавляют 1 час
        return max(1, ceil($palletsCount / 33));
    }

    public function gate()
    {
        return $this->belongsTo(Gate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}