<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (strlen($digits) !== 14 || preg_match('/^(\d)\1{13}$/', $digits)) {
            $fail('Informe um CNPJ valido.');

            return;
        }

        $firstCheckDigit = $this->calculateCheckDigit(substr($digits, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $secondCheckDigit = $this->calculateCheckDigit(substr($digits, 0, 12).$firstCheckDigit, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        if (! str_ends_with($digits, (string) $firstCheckDigit.(string) $secondCheckDigit)) {
            $fail('Informe um CNPJ valido.');
        }
    }

    /**
     * @param  array<int, int>  $factors
     */
    private function calculateCheckDigit(string $value, array $factors): int
    {
        $total = 0;

        foreach ($factors as $index => $factor) {
            $total += ((int) $value[$index]) * $factor;
        }

        $remainder = $total % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
