<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidContactEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Please enter a valid email address.');

            return;
        }

        $email = strtolower(trim($value));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('Please enter a valid email address.');

            return;
        }

        if (substr_count($email, '@') !== 1) {
            $fail('Please enter a valid email address.');

            return;
        }

        [$local, $domain] = explode('@', $email, 2);

        if ($local === '' || $domain === '' || strlen($local) > 64) {
            $fail('Please enter a valid email address.');

            return;
        }

        if (str_contains($local, '..') || str_starts_with($local, '.') || str_ends_with($local, '.')) {
            $fail('The email address format is not valid.');

            return;
        }

        if (! preg_match('/^[a-z0-9](?:[a-z0-9._%+-]*[a-z0-9])?$/', $local)) {
            $fail('The email address format is not valid.');

            return;
        }

        if (! preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $domain)) {
            $fail('Please enter a valid email domain.');

            return;
        }

        if (preg_match('/\.(com|net|org|co|io)\.(com|net|org|co|uk|tz)$/i', $domain)) {
            $fail('This email domain looks incorrect. Please double-check your address.');

            return;
        }

        if (preg_match('/@(example\.(com|org|net)|test\.com|localhost)$/i', $email)) {
            $fail('Please use a real email address we can reply to.');

            return;
        }
    }
}
