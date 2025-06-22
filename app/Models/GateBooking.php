<?php

namespace App\Models;

class GateBooking extends BaseModel
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

    public function gate()
    {
        return $this->belongsTo(Gate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}