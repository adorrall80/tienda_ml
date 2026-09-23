<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NoReservedAttackWords implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $text = mb_strtolower((string) $value);

        foreach ($this->blockedTerms() as $term) {
            $needle = mb_strtolower((string) $term);

            if ($needle === '') {
                continue;
            }

            if (str_contains($needle, ':') || str_contains($needle, '<') || str_contains($needle, '/')) {
                if (str_contains($text, $needle)) {
                    $fail('El campo :attribute contiene una palabra o patron no permitido.');
                    return;
                }

                continue;
            }

            $pattern = '/(?<![[:alnum:]_])'.preg_quote($needle, '/').'(?![[:alnum:]_])/iu';

            if (preg_match($pattern, $text) === 1) {
                $fail('El campo :attribute contiene una palabra o patron no permitido.');
                return;
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function blockedTerms(): array
    {
        $configured = config('security.blocked_text_terms', []);

        if (! Schema::hasTable('security_blocked_terms')) {
            return $configured;
        }

        $managed = DB::table('security_blocked_terms')
            ->where('active', true)
            ->pluck('term')
            ->all();

        return array_values(array_unique(array_filter([
            ...$configured,
            ...$managed,
        ])));
    }
}
