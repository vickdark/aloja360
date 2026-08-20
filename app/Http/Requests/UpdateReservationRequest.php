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
