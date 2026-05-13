<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
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
            'note.max'      => 'La sugerencia no puede superar los 1000 caracteres.',
        ];
    }
}
