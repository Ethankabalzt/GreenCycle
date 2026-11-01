<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la ruta ya exige auth:sanctum
    }

    public function rules(): array
    {

        return [
            'seed_type_id' => ['required', 'integer', 'exists:seed_types,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'seed_type_id.exists' => 'El tipo de semilla no existe.',
        ];
    }
}
