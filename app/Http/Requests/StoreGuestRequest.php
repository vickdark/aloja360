<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:50',
            'document_number' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'country' => 'nullable|string|max:2',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'marketing_consent' => 'boolean',
        ];
    }
}
