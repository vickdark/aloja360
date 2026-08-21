<?php

namespace App\Http\Requests;

use App\Enums\PricingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'accommodation_id' => ['sometimes', 'required', 'integer', 'exists:accommodations,id'],
            'pricing_type' => ['nullable', new Enum(PricingType::class)],
            'is_day_pass' => ['nullable', 'boolean'],
            'primary_guest_id' => ['sometimes', 'required', 'integer', 'exists:guests,id'],
            'guests_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'adults_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'children_count' => ['nullable', 'integer', 'min:0'],
            'guest_notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:50'],
            'check_in_date' => ['sometimes', 'required', 'date'],
            'check_out_date' => ['sometimes', 'required', 'date'],
            'cleaning_fee' => ['nullable', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'tax_total' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable'],
            'origin_channel' => ['nullable', 'string'],
        ];
    }
}
