<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCleaningTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_to' => 'nullable|exists:usuarios,id',
            'status' => 'required|string',
            'cleaner_notes' => 'nullable|string',
            'supervisor_notes' => 'nullable|string',
            'quality_score' => 'nullable|integer|min:1|max:5',
        ];
    }
}
