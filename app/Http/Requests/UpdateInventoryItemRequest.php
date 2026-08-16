<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accommodation_id' => 'nullable|exists:accommodations,id',
            'category' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:inventory_items,sku,' . $this->inventory_item->id,
            'barcode' => 'nullable|string|max:100',
            'expected_quantity' => 'required|integer|min:0',
            'current_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_value' => 'nullable|numeric|min:0',
            'replacement_cost' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'is_consumable' => 'boolean',
            'reorder_threshold' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
