<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueCnpjAcrossEntities implements ValidationRule
{
    public function __construct(
        private readonly ?string $ignoreTable = null,
        private readonly int|string|null $ignoreId = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cnpj = preg_replace('/\D+/', '', (string) $value);

        if ($cnpj === '') {
            return;
        }

        $existsInSchools = DB::table('schools')
            ->when(
                $this->ignoreTable === 'schools' && $this->ignoreId !== null,
                fn ($query) => $query->where('id', '!=', $this->ignoreId),
            )
            ->where('cnpj', $cnpj)
            ->exists();

        $existsInPoints = DB::table('point_of_schools')
            ->when(
                $this->ignoreTable === 'point_of_schools' && $this->ignoreId !== null,
                fn ($query) => $query->where('id', '!=', $this->ignoreId),
            )
            ->where('cnpj', $cnpj)
            ->exists();

        if ($existsInSchools || $existsInPoints) {
            $fail('Este CNPJ ja esta em uso no sistema.');
        }
    }
}
