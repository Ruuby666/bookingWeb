<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:reservations,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'El ID de la reserva es obligatorio.',
            'event_id.exists' => 'La reserva indicada no existe.',
            'start_time.required' => 'La hora de entrada es obligatoria.',
            'start_time.date_format' => 'La hora de entrada debe tener el formato HH:MM.',
            'end_time.required' => 'La hora de salida es obligatoria.',
            'end_time.date_format' => 'La hora de salida debe tener el formato HH:MM.',
        ];
    }
}
