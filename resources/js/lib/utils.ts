import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function onlyDigits(value: string): string {
    return value.replace(/\D/g, '');
}

export function hasCompleteCnpj(value: string): boolean {
    return onlyDigits(value).length === 14;
}

export function isValidCnpj(value: string): boolean {
    const digits = onlyDigits(value);

    if (digits.length !== 14 || /^(\d)\1+$/.test(digits)) {
        return false;
    }

    const calculateCheckDigit = (base: string, factors: number[]) => {
        const total = factors.reduce((sum, factor, index) => sum + Number(base[index]) * factor, 0);
        const remainder = total % 11;

        return remainder < 2 ? 0 : 11 - remainder;
    };

    const firstCheckDigit = calculateCheckDigit(digits.slice(0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
    const secondCheckDigit = calculateCheckDigit(digits.slice(0, 12) + firstCheckDigit, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

    return digits.endsWith(`${firstCheckDigit}${secondCheckDigit}`);
}

export function formatCnpj(value: string): string {
    const digits = onlyDigits(value).slice(0, 14);

    if (!digits) {
        return '';
    }

    return digits
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
}

export function formatCep(value: string): string {
    const digits = onlyDigits(value).slice(0, 8);

    if (!digits) {
        return '';
    }

    return digits.replace(/^(\d{5})(\d)/, '$1-$2');
}

export function hasCompleteCep(value: string): boolean {
    return onlyDigits(value).length === 8;
}

export function isValidCep(value: string): boolean {
    return hasCompleteCep(value);
}
