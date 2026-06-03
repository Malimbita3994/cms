<?php

namespace App\Support;

final class ServiceContentItems
{
    /**
     * @return array<string, string>
     */
    public static function iconOptions(): array
    {
        return [
            'CommandLine' => 'Command line / code',
            'Window' => 'Window / application',
            'Sparkles' => 'Sparkles / consulting',
            'GlobeAlt' => 'Globe / integration',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array{id: int|null, title: string, description: mixed, icon: string}>
     */
    public static function forForm(array $rows): array
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
                'id' => isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null,
                'title' => $title,
                'description' => $row['description'] ?? '',
                'icon' => trim((string) ($row['icon'] ?? 'CommandLine')),
            ];
        }

        return $items !== [] ? $items : [
            ['id' => null, 'title' => '', 'description' => '', 'icon' => 'CommandLine'],
        ];
    }
}
