<?php

namespace App\Http\Requests;

use App\Models\Sindicato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSindicatoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canModifyCi() ?? false;
    }

    public function rules(): array
    {
        $sindicato = $this->route('sindicato');

        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('sindicatos', 'nombre')->ignore($sindicato)],
            'sigla' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', Rule::in(Sindicato::statuses())],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
