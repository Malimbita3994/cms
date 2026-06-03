<?php

namespace App\Support;

/**
 * Normalizes About section repeater rows for strengths, approach steps, and values.
 */
final class AboutContentItems
{
    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{title: string, description: string}>
     */
    public static function strengthsForForm(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            if (is_string($row)) {
                $title = trim($row);
                if ($title !== '') {
                    $items[] = ['title' => $title, 'description' => ''];
                }

                continue;
            }

            if (! is_array($row)) {
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $items[] = [
                'title' => $title,
                'description' => trim((string) ($row['description'] ?? $row['desc'] ?? '')),
            ];
        }

        return $items !== [] ? $items : [['title' => '', 'description' => '']];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array{title: string, description: string}>
     */
    public static function strengthsForStorage(array $rows): array
    {
        return array_values(array_filter(array_map(static function (array $row): ?array {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                return null;
            }

            return [
                'title' => $title,
                'description' => trim((string) ($row['description'] ?? '')),
            ];
        }, $rows)));
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{step: string}>
     */
    public static function approachForForm(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            if (is_string($row)) {
                $step = trim($row);
                if ($step !== '') {
                    $items[] = ['step' => $step];
                }

                continue;
            }

            if (! is_array($row)) {
                continue;
            }

            $step = trim((string) ($row['step'] ?? $row['title'] ?? ''));
            if ($step !== '') {
                $items[] = ['step' => $step];
            }
        }

        return $items !== [] ? $items : [['step' => '']];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, string>
     */
    public static function approachForStorage(array $rows): array
    {
        return array_values(array_filter(array_map(
            static fn (array $row): string => trim((string) ($row['step'] ?? '')),
            $rows,
        )));
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{title: string, icon: string}>
     */
    public static function valuesForForm(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $items[] = [
                'title' => $title,
                'icon' => (string) ($row['icon'] ?? 'Shield'),
            ];
        }

        return $items !== [] ? $items : [['title' => '', 'icon' => 'Shield']];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array{title: string, icon: string}>
     */
    public static function valuesForStorage(array $rows): array
    {
        return array_values(array_filter(array_map(static function (array $row): ?array {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                return null;
            }

            return [
                'title' => $title,
                'icon' => (string) ($row['icon'] ?? 'Shield'),
            ];
        }, $rows)));
    }

    /**
     * @return array<string, string>
     */
    public static function valueIconOptions(): array
    {
        return [
            'Shield' => 'Shield',
            'Lightbulb' => 'Lightbulb',
            'Trophy' => 'Trophy',
            'Target' => 'Target',
            'Handshake' => 'Handshake',
            'ShieldCheck' => 'Shield Check',
            'HeartHandshake' => 'Heart Handshake',
            'Rocket' => 'Rocket',
        ];
    }
}
