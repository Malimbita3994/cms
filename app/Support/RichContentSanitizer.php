<?php

namespace App\Support;

final class RichContentSanitizer
{
    private const ALLOWED_ALIGNMENTS = ['start', 'center', 'end', 'justify', 'left', 'right'];

    private const ALLOWED_TAGS = '<p><br><strong><em><b><i><u><a><ul><ol><li><h2><h3><blockquote><span>';

    /**
     * Allow a small set of tags for homepage copy (TipTap HTML output).
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $alignments = self::extractTextAlignments($html);
        $stripped = trim(strip_tags($html, self::ALLOWED_TAGS));

        return self::applyTextAlignments($stripped, $alignments);
    }

    /**
     * @return array<int, string|null>
     */
    private static function extractTextAlignments(string $html): array
    {
        $alignments = [];

        if (! preg_match_all('/<(p|h2|h3)\b[^>]*>/i', $html, $tags, PREG_SET_ORDER)) {
            return $alignments;
        }

        foreach ($tags as $tag) {
            $attrs = $tag[0];
            if (preg_match('/style\s*=\s*["\']([^"\']*)["\']/i', $attrs, $styleMatch)
                && preg_match('/text-align\s*:\s*([a-z]+)/i', $styleMatch[1], $align)
                && in_array(strtolower($align[1]), self::ALLOWED_ALIGNMENTS, true)) {
                $alignments[] = strtolower($align[1]);
            } else {
                $alignments[] = null;
            }
        }

        return $alignments;
    }

    /**
     * @param  array<int, string|null>  $alignments
     */
    private static function applyTextAlignments(string $html, array $alignments): string
    {
        $index = 0;

        return preg_replace_callback(
            '/<(p|h2|h3)(\s[^>]*)?>/i',
            function (array $match) use ($alignments, &$index): string {
                $tag = strtolower($match[1]);
                $align = $alignments[$index] ?? null;
                $index++;

                if ($align) {
                    return '<'.$tag.' style="text-align: '.$align.'">';
                }

                return '<'.$tag.'>';
            },
            $html,
        ) ?? $html;
    }
}
