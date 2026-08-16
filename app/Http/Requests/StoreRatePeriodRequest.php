<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatePeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'required|exists:accommodations,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'days_of_week' => 'nullable|array',
            'is_weekend' => 'boolean',
            'is_holiday' => 'boolean',
            'price_per_night' => 'required|numeric|min:0',
            'extra_guest_price' => 'nullable|numeric|min:0',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1',
            'status' => 'required|string|in:active,inactive',
            'priority' => 'nullable|integer',
            'notes' => 'nullable|string',
        ];
    }
}
