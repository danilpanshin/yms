<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGateBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'gate_id' => 'exists:gates,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'pallets_count' => 'required|integer|min:1',
            'purpose' => 'string|max:255'
        ];
    }
}

