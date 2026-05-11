<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Delegates fine-grained authorization to PropertyPolicy via controller
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['required', 'string'],
            'location'        => ['required', 'string', 'max:255'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'min_nights'      => ['required', 'numeric', 'min:1'],
            'capacity'        => ['required', 'integer', 'min:1'],
            'size'            => ['required', 'integer', 'min:0'],
            'bedrooms'        => ['required', 'string'],
            'bathrooms'       => ['required', 'integer', 'min:0'],
            'images_div'      => ['required', 'string', 'max:255'],
            'tv'              => ['nullable', 'string', 'max:100'],
            'entertainment'   => ['required', 'boolean'],
            'parking'         => ['required', 'boolean'],
            'pool'            => ['required', 'boolean'],
            'garden'          => ['required', 'boolean'],
            'safeBox'         => ['required', 'boolean'],
            'terrace'         => ['required', 'boolean'],
            'wifi'            => ['required', 'boolean'],
            'lat'             => ['required', 'numeric', 'between:-90,90'],
            'lng'             => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
