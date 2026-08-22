<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCleaningTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'required|exists:accommodations,id',
            'assigned_name' => 'nullable|string|max:255',
            'type' => 'required|string|max:50',
            'status' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0|max:999999999999.99',
        ];
    }
}
