<?php

namespace App\Support;

final class ContactMessageSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><ul><ol><li><a><h2><h3><blockquote>';

    public static function clean(string $html): string
    {
        $clean = strip_tags($html, self::ALLOWED_TAGS);
        $clean = preg_replace('/\s(href|src)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $clean) ?? $clean;

        return trim($clean);
    }

    public static function plainText(string $html): string
    {
        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
