<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'accommodation_id' => 'nullable|exists:accommodations,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:100',
        ];
    }
}
