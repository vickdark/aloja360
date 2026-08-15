<?php

namespace App\Http\Requests;

use App\Models\Accommodation;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $businessId = $this->input('business_id');

        if (! $businessId) {
            return false;
        }

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $this->user();

        return $user && $user->hasPermission('reservations.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'accommodation_id' => [
                'required',
                'integer',
                'exists:accommodations,id',
                // Asegurarse de que el alojamiento pertenezca al mismo negocio
                function ($attribute, $value, $fail) {
                    $accommodation = Accommodation::find($value);
                    if ($accommodation && $accommodation->business_id != $this->input('business_id')) {
                        $fail('El alojamiento no pertenece al negocio especificado.');
                    }
                },
            ],
            'primary_guest_id' => ['required', 'integer', 'exists:guests,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests_count' => ['required', 'integer', 'min:1'],
            'adults_count' => ['required', 'integer', 'min:1'],
            'children_count' => ['nullable', 'integer', 'min:0'],
            'source' => ['nullable', 'string', 'max:50'],
            'guest_notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ];
    }
}
