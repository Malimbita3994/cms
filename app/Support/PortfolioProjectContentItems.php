<?php

namespace App\Support;

final class PortfolioProjectContentItems
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
                'preview' => $row['preview'] ?? null,
                'title' => $title,
                'description' => $row['description'] ?? '',
                'technologies' => LineDelimitedText::toForm($row['technologies'] ?? null),
                'achievements' => LineDelimitedText::toForm($row['achievements'] ?? null),
            ];
        }

        return $items !== [] ? $items : [
            [
                'id' => null,
                'image' => null,
                'preview' => null,
                'title' => '',
                'description' => '',
                'technologies' => '',
                'achievements' => '',
            ],
        ];
    }
}
