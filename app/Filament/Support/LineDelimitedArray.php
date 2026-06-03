<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;

final class LineDelimitedArray
{
    /**
     * JSON/array-backed attributes edited as one value per line (matches legacy TS arrays).
     */
    public static function textarea(string $name, string $label, bool $required = true): Textarea
    {
        return Textarea::make($name)
            ->label($label)
            ->helperText('One item per line.')
            ->required($required)
            ->formatStateUsing(fn (?array $state): string => $state === null || $state === [] ? '' : implode("\n", $state))
            ->dehydrateStateUsing(function (?string $state): array {
                if ($state === null || $state === '') {
                    return [];
                }

                return array_values(array_filter(array_map(trim(...), preg_split('/\r\n|\r|\n/', $state))));
            })
            ->columnSpanFull();
    }
}
