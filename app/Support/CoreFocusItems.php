<?php

namespace App\Support;

/**
 * Normalizes core focus strengths between legacy string rows and { heading, text } objects.
 */
final class CoreFocusItems
{
    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{heading: string, text: string}>
     */
    public static function forForm(array $rows, string $defaultText = ''): array
    {
        $items = self::normalize($rows, $defaultText);

        if ($items === []) {
            return [
                ['heading' => '', 'text' => ''],
            ];
        }

        return $items;
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{heading: string, text: string}>
     */
    public static function forStorage(array $rows): array
    {
        return array_values(array_filter(
            array_map(static function (array $row): ?array {
                $heading = trim((string) ($row['heading'] ?? $row['title'] ?? ''));
                if ($heading === '') {
                    return null;
                }

                $text = RichContentSanitizer::clean($row['text'] ?? '') ?? trim((string) ($row['text'] ?? ''));

                return [
                    'heading' => $heading,
                    'text' => $text,
                ];
            }, self::ensureRepeaterRows($rows)),
        ));
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{heading: string, text: string}>
     */
    public static function forApi(array $rows, string $fallbackBlurb): array
    {
        $fallback = RichContentSanitizer::clean($fallbackBlurb) ?? $fallbackBlurb;

        return array_map(
            static fn (array $item): array => [
                'heading' => $item['heading'],
                'text' => $item['text'] !== '' ? $item['text'] : $fallback,
            ],
            self::normalize($rows, $fallback),
        );
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{heading: string, text: string}>
     */
    private static function normalize(array $rows, string $defaultText): array
    {
        $items = [];

        foreach ($rows as $row) {
            if (is_string($row)) {
                $heading = trim($row);
                if ($heading === '') {
                    continue;
                }
                $items[] = ['heading' => $heading, 'text' => $defaultText];

                continue;
            }

            if (! is_array($row)) {
                continue;
            }

            $heading = trim((string) ($row['heading'] ?? $row['title'] ?? ''));
            if ($heading === '') {
                continue;
            }

            $text = $row['text'] ?? $row['description'] ?? $row['body'] ?? '';
            if (is_string($text)) {
                $text = trim($text);
            } else {
                $text = '';
            }

            $items[] = [
                'heading' => $heading,
                'text' => $text !== '' ? $text : $defaultText,
            ];
        }

        return $items;
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array<string, mixed>>
     */
    private static function ensureRepeaterRows(array $rows): array
    {
        return array_values(array_filter($rows, static fn (mixed $row): bool => is_array($row)));
    }
}
