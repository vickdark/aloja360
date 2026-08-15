<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');

        if (!$reservation) {
            return false;
        }

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $this->user();

        // El usuario debe pertenecer al negocio de la reserva
        return $user && $user->belongsToBusiness($reservation->business_id);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // No permitimos cambiar business_id ni accommodation_id en update simple.
            // Para cambiar de alojamiento o fechas se requeriría una validación completa de disponibilidad (mejor otra action).
            'primary_guest_id' => ['sometimes', 'required', 'integer', 'exists:guests,id'],
            'guests_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'adults_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'children_count' => ['nullable', 'integer', 'min:0'],
            'guest_notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:50'],
        ];
    }
}
