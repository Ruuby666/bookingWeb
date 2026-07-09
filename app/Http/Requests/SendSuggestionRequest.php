<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class SendSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Reservation::where('id', $this->route('id'))
            ->whereHas('property', fn ($q) => $q->where('owner_id', $this->user()?->id))
            ->exists();
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'El mensaje de sugerencia es obligatorio.',
            'note.max' => 'La sugerencia no puede superar los 1000 caracteres.',
        ];
    }
}
