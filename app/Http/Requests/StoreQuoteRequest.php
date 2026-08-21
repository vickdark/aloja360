<?php

namespace App\Http\Requests;

use App\Enums\PricingType;
use App\Enums\QuoteStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation (sanitizing financial fields to 0 when empty).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cleaning_fee' => ($this->filled('cleaning_fee') && is_numeric($this->input('cleaning_fee'))) ? $this->input('cleaning_fee') : 0,
            'security_deposit' => ($this->filled('security_deposit') && is_numeric($this->input('security_deposit'))) ? $this->input('security_deposit') : 0,
            'discount_total' => ($this->filled('discount_total') && is_numeric($this->input('discount_total'))) ? $this->input('discount_total') : 0,
            'tax_total' => ($this->filled('tax_total') && is_numeric($this->input('tax_total'))) ? $this->input('tax_total') : 0,
            'children_count' => ($this->filled('children_count') && is_numeric($this->input('children_count'))) ? (int)$this->input('children_count') : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'required|exists:accommodations,id',
            'pricing_type' => ['nullable', new Enum(PricingType::class)],
            'is_day_pass' => ['nullable', 'boolean'],
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date|after:today',
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
            'status' => ['sometimes', new Enum(QuoteStatus::class)],
        ];
    }
}
