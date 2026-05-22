<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAffiliateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageAffiliates() ?? false;
    }

    public function rules(): array
    {
        $affiliate = $this->route('affiliate');

        $rules = [
            'nombres' => ['required', 'string', 'max:150'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'lugar_fecha_nacimiento' => ['nullable', 'string', 'max:255'],
            'nacionalidad' => ['nullable', 'string', 'max:100'],
            'domicilio' => ['nullable', 'string', 'max:255'],
            'celular' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'idioma_castellano' => ['nullable', 'boolean'],
            'idioma_ingles' => ['nullable', 'boolean'],
            'idioma_aymara' => ['nullable', 'boolean'],
            'idioma_quechua' => ['nullable', 'boolean'],
            'idioma_otros' => ['nullable', 'string', 'max:255'],
            'formacion_academica' => ['nullable', 'array', 'max:3'],
            'formacion_academica.*.carrera' => ['nullable', 'string', 'max:255'],
            'formacion_academica.*.universidad' => ['nullable', 'string', 'max:255'],
            'formacion_academica.*.titulo_academico_numero' => ['nullable', 'string', 'max:100'],
            'formacion_academica.*.titulo_academico_fecha' => ['nullable', 'date'],
            'formacion_academica.*.titulo_provision_nacional_numero' => ['nullable', 'string', 'max:100'],
            'formacion_academica.*.titulo_provision_nacional_fecha' => ['nullable', 'date'],
            'diplomado_universidad' => ['nullable', 'string', 'max:255'],
            'diplomado_anio' => ['nullable', 'string', 'max:10'],
            'diplomado_titulo' => ['nullable', 'string', 'max:255'],
            'especialidad_universidad' => ['nullable', 'string', 'max:255'],
            'especialidad_anio' => ['nullable', 'string', 'max:10'],
            'especialidad_titulo' => ['nullable', 'string', 'max:255'],
            'maestria_universidad' => ['nullable', 'string', 'max:255'],
            'maestria_anio' => ['nullable', 'string', 'max:10'],
            'maestria_titulo' => ['nullable', 'string', 'max:255'],
            'lugar_trabajo' => ['nullable', 'string', 'max:255'],
            'red_salud' => ['nullable', 'string', 'max:255'],
            'item_principal' => ['nullable', 'string', 'max:100', Rule::unique('affiliates', 'item_principal')->ignore($affiliate)],
            'item_secundario' => ['nullable', 'string', 'max:100'],
            'tipo_item' => ['nullable', Rule::in(['SEDES', 'MINISTERIAL'])],
            'fecha_ingreso_sistema' => ['nullable', 'date'],
            'fecha_primer_descuento_fesirmes' => ['nullable', 'date'],
            'tematica_capacitacion' => ['nullable', 'string', 'max:1000'],
            'deportes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->user()?->role->canModifyCi()) {
            $rules['ci'] = ['required', 'string', 'max:30', Rule::unique('affiliates', 'ci')->ignore($affiliate)];
            $rules['status'] = ['required', Rule::in(['activo', 'baja', 'suspendido', 'observado'])];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idioma_castellano' => $this->boolean('idioma_castellano'),
            'idioma_ingles' => $this->boolean('idioma_ingles'),
            'idioma_aymara' => $this->boolean('idioma_aymara'),
            'idioma_quechua' => $this->boolean('idioma_quechua'),
            'item_principal' => $this->filled('item_principal') ? $this->input('item_principal') : null,
        ]);

        if (! $this->user()?->role->canModifyCi()) {
            $this->request->remove('ci');
            $this->request->remove('status');
        }
    }
}
