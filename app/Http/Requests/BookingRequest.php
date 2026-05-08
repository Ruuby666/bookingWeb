<?php

namespace App\Http\Requests;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $property = Property::find($this->input('property_id'));
        $capacity = $property?->capacity ?? 1;

        return [
            'property_id'        => ['required', 'exists:properties,id'],
            'adults'             => ['required', 'integer', 'min:1', 'max:' . $capacity],
            'children'           => ['required', 'integer', 'min:0', 'max:' . ($capacity - 1)],
            'name'               => ['required', 'string', 'max:255'],
            'number'             => ['required', 'string', 'max:20'],
            'email'              => ['required', 'email', 'max:255'],
            'verification_email' => ['required', 'same:email', 'email', 'max:255'],
            'message'            => ['nullable', 'string', 'max:1000'],
            'daterange'          => ['required', 'string', 'regex:/\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}/'],
            'total_price'        => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        $property = Property::find($this->input('property_id'));
        $capacity = $property?->capacity ?? 1;

        return [
            'adults.required'             => 'El número de adultos es obligatorio.',
            'adults.min'                  => 'Debe haber al menos 1 adulto.',
            'adults.max'                  => "El número de adultos no puede exceder {$capacity}.",
            'children.required'           => 'El número de niños es obligatorio.',
            'children.min'                => 'El número de niños no puede ser negativo.',
            'children.max'                => 'El número de niños no puede exceder ' . ($capacity - 1) . '.',
            'name.required'               => 'El nombre es obligatorio.',
            'name.string'                 => 'El nombre debe ser texto.',
            'name.max'                    => 'El nombre no puede tener más de 255 caracteres.',
            'number.required'             => 'El número de teléfono es obligatorio.',
            'number.max'                  => 'El número de teléfono no puede tener más de 20 caracteres.',
            'email.required'              => 'El correo electrónico es obligatorio.',
            'email.email'                 => 'Debe ser un correo electrónico válido.',
            'email.max'                   => 'El correo electrónico no puede tener más de 255 caracteres.',
            'verification_email.required' => 'La verificación del correo es obligatoria.',
            'verification_email.same'     => 'El correo de verificación debe coincidir con el correo electrónico.',
            'verification_email.email'    => 'Debe ser un correo electrónico válido.',
            'message.string'              => 'El mensaje debe ser texto.',
            'message.max'                 => 'El mensaje no puede tener más de 1000 caracteres.',
            'daterange.required'          => 'El rango de fechas es obligatorio.',
            'daterange.regex'             => 'El rango de fechas debe tener el formato DD/MM/AAAA - DD/MM/AAAA.',
            'total_price.min'             => 'Selecciona al menos el mínimo de noches.',
        ];
    }

    /**
     * Additional validation after basic rules pass.
     * Validates that total guests do not exceed property capacity.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $property    = Property::find($this->input('property_id'));
            $totalGuests = (int) $this->input('adults', 0) + (int) $this->input('children', 0);

            if ($property && $totalGuests > $property->capacity) {
                $validator->errors()->add(
                    'adults',
                    "El número total de personas ({$totalGuests}) no puede exceder la capacidad de la propiedad ({$property->capacity})."
                );
            }
        });
    }
}
