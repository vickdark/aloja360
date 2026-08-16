<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string',
            'assigned_to' => 'nullable|exists:usuarios,id',
            'technician_notes' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
            'actual_cost' => 'nullable|numeric|min:0',
        ];
    }
}
