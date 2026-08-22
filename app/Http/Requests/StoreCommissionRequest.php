<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'required|exists:accommodations,id',
            'beneficiary_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999999999.99',
            'commission_date' => 'required|date',
            'status' => 'required|string|in:pending,paid,cancelled',
            'paid_date' => 'nullable|date|required_if:status,paid',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'paid_date.required_if' => 'Indica la fecha de pago cuando la comisión está pagada.',
        ];
    }
}
