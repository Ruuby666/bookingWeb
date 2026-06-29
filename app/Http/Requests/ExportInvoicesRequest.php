<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportInvoicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'min:1'],
            'invoice_amount' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // ensure ids[] query parameters are normalized to array in body
        if ($this->query('ids')) {
            $this->merge(['ids' => $this->query('ids')]);
        }

        if ($this->query('invoice_amount')) {
            $this->merge(['invoice_amount' => $this->query('invoice_amount')]);
        }
    }
}
