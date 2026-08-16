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
            'assigned_to' => 'nullable|exists:usuarios,id',
            'type' => 'required|string|max:50',
            'status' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'description' => 'nullable|string',
        ];
    }
}
