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
            'accommodation_id' => ['required', function($attr,$value,$fail){
                if ($value === 'all') return;
                if (!\App\Models\Accommodation::where('id',$value)->exists()) $fail('El alojamiento seleccionado no es válido.');
            }],
            'apply_to_all' => 'nullable|boolean',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'days_of_week' => 'nullable|array',
            'is_weekend' => 'boolean',
            'is_holiday' => 'boolean',
            'price_per_night' => 'nullable|numeric|min:0',
            'adjustment_type' => 'required|string|in:amount,percentage',
            'adjustment_value' => 'required|numeric|min:0',
            'child_adjustment_type' => 'nullable|string|in:amount,percentage',
            'child_adjustment_value' => 'nullable|numeric|min:0',
            'accommodation_adjustment_type' => 'nullable|string|in:amount,percentage',
            'accommodation_adjustment_value' => 'nullable|numeric|min:0',
            'extra_guest_price' => 'nullable|numeric|min:0',
            'extra_child_price' => 'nullable|numeric|min:0',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1',
            'status' => 'required|string|in:active,inactive',
            'priority' => 'nullable|integer',
            'notes' => 'nullable|string',
        ];
    }
}
