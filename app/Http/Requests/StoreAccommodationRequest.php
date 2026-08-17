<?php

namespace App\Http\Requests;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Enums\PricingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAccommodationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:accommodations,code',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:accommodations,slug',
            'type' => ['required', new Enum(AccommodationType::class)],
            'status' => ['required', new Enum(AccommodationStatus::class)],
            'description' => 'nullable|string',
            'max_guests' => 'required|integer|min:1',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'beds' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'pricing_type' => ['required', new Enum(PricingType::class)],
            'price_per_person' => 'nullable|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'weekend_price_modifier' => 'nullable|numeric|min:0',
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'house_rules' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ];
    }
}
