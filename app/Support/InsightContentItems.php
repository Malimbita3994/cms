<?php

namespace App\Support;

final class InsightContentItems
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
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
                'image' => $row['image'] ?? null,
                'title' => $title,
                'excerpt' => $row['excerpt'] ?? '',
                'display_date' => $row['display_date'] ?? null,
            ];
        }

        return $items !== [] ? $items : [
            [
                'id' => null,
                'image' => null,
                'title' => '',
                'excerpt' => '',
                'display_date' => null,
            ],
        ];
    }
}
