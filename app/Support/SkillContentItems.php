<?php

namespace App\Support;

/**
 * Normalizes skill repeater rows for the Skills admin editor.
 */
final class SkillContentItems
{
    /**
     * @return array<string, string>
     */
    public static function iconOptions(): array
    {
        return [
            'ClipboardDocumentList' => 'Clipboard / document',
            'RectangleStack' => 'Stack / layers',
            'ChartBarSquare' => 'Chart / analytics',
            'Squares2X2' => 'Grid / architecture',
            'ArrowPath' => 'Cycle / process',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array{id: int|null, image: mixed, name: string, level: int, focus: string, icon: string}>
     */
    public static function forForm(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $id = isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null;

            $items[] = [
                'id' => $id,
                'image' => $row['image'] ?? null,
                'name' => $name,
                'level' => (int) ($row['level'] ?? 0),
                'focus' => trim((string) ($row['focus'] ?? '')),
                'icon' => trim((string) ($row['icon'] ?? 'ClipboardDocumentList')),
            ];
        }

        return $items !== [] ? $items : [
            [
                'id' => null,
                'image' => null,
                'name' => '',
                'level' => 80,
                'focus' => '',
                'icon' => 'ClipboardDocumentList',
            ],
        ];
    }
};
