<?php

namespace App\Http\Requests;

use App\Models\Sindicato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSindicatoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canModifyCi() ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:sindicatos,nombre'],
            'sigla' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', Rule::in(Sindicato::statuses())],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
