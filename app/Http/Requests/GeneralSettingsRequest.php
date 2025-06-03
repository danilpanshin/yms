<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest {
    public function rules(): array
    {
        return [
            'supplier_list_limit' => 'required|integer',
            'supplier_list_per_page' => 'required|integer',
        ];
    }
}