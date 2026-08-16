<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'required|exists:accommodations,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string',
            'category' => 'required|string|max:100',
            'status' => 'required|string',
            'assigned_to' => 'nullable|exists:usuarios,id',
            'scheduled_at' => 'nullable|date',
            'blocks_accommodation' => 'boolean',
        ];
    }
}
