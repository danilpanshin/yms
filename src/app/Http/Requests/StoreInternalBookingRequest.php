<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternalBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'gate_ids' => 'required|array',
            'gate_ids.*' => 'exists:gates,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ];
    }
}