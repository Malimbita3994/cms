<?php

namespace App\Support;

final class LineDelimitedText
{
    /**
     * @param  array<int, string>|null  $items
     */
    public static function toForm(?array $items): string
    {
        if ($items === null || $items === []) {
            return '';
        }

        return implode("\n", array_map(strval(...), $items));
    }

    /**
     * @return array<int, string>
     */
    public static function toStorage(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        return array_values(array_filter(array_map(trim(...), preg_split('/\r\n|\r|\n/', $text))));
    }
}
