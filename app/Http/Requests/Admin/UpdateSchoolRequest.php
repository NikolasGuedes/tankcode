<?php

namespace App\Http\Requests\Admin;

use App\Models\School;
use App\Rules\UniqueCnpjAcrossEntities;
use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cnpj' => $this->filled('cnpj') ? Str::of($this->string('cnpj')->value())->replaceMatches('/\D+/', '')->value() : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var School $school */
        $school = $this->route('school');

        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'digits:14', new ValidCnpj, new UniqueCnpjAcrossEntities('schools', $school->id)],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da escola e obrigatorio.',
            'cnpj.required' => 'O CNPJ da escola e obrigatorio.',
            'cnpj.digits' => 'Informe um CNPJ valido com 14 digitos.',
            'logo.image' => 'Envie um arquivo de imagem valido para a logo.',
            'logo.mimes' => 'A logo deve estar em JPG, PNG, WEBP ou SVG.',
            'logo.max' => 'A logo deve ter no maximo 2 MB.',
            'status.required' => 'O status da escola e obrigatorio.',
        ];
    }
}
