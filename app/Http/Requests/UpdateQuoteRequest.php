<?php

namespace App\Http\Requests;

use App\Enums\PricingType;
use App\Enums\QuoteStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'required|exists:accommodations,id',
            'pricing_type' => ['nullable', new Enum(PricingType::class)],
            'is_day_pass' => ['nullable', 'boolean'],
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'adults_count' => 'required|integer|min:1',
            'children_count' => 'nullable|integer|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'tax_total' => 'nullable|numeric|min:0',
            'guest_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'expires_at' => 'nullable|date|after:check_in_date',
            'status' => ['required', new Enum(QuoteStatus::class)],
        ];
    }
}
