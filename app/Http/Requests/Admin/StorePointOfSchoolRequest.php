<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueCnpjAcrossEntities;
use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePointOfSchoolRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cnpj' => $this->filled('cnpj') ? Str::of($this->string('cnpj')->value())->replaceMatches('/\D+/', '')->value() : null,
            'zip_code' => $this->filled('zip_code') ? Str::of($this->string('zip_code')->value())->replaceMatches('/\D+/', '')->value() : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer', Rule::exists('schools', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'digits:14', new ValidCnpj, new UniqueCnpjAcrossEntities],
            'zip_code' => ['required', 'digits:8'],
            'address_line' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'A escola e obrigatoria.',
            'name.required' => 'O nome do ponto de ensino e obrigatorio.',
            'cnpj.required' => 'O CNPJ do ponto de ensino e obrigatorio.',
            'cnpj.digits' => 'Informe um CNPJ valido com 14 digitos.',
            'zip_code.required' => 'O CEP e obrigatorio.',
            'zip_code.digits' => 'Informe um CEP valido com 8 digitos.',
            'address_line.required' => 'O endereco e obrigatorio.',
            'status.required' => 'O status do ponto de ensino e obrigatorio.',
        ];
    }
}
