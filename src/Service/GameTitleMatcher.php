<?php
declare(strict_types=1);

namespace App\Service;

/**
 * Picks the best RAWG candidate for a given (typically Steam) game title
 * using a simple normalized string-similarity score.
 */
class GameTitleMatcher
{
    protected const CONFIDENCE_THRESHOLD = 55.0;

    /**
     * @param string $title Title to match, e.g. from a Steam library entry.
     * @param array<int, array<string, mixed>> $candidates Normalized RAWG search results.
     * @return array<string, mixed>|null The best candidate, or null if nothing is confident enough.
     */
    public static function bestMatch(string $title, array $candidates): ?array
    {
        $normalizedTitle = self::normalize($title);
        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $candidate) {
            similar_text($normalizedTitle, self::normalize((string)$candidate['title']), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best = $candidate;
            }
        }

        if ($best === null || $bestScore < self::CONFIDENCE_THRESHOLD) {
            return null;
        }

        return $best;
    }

    protected static function normalize(string $title): string
    {
        $title = strtolower($title);
        $title = preg_replace('/[^a-z0-9]+/', ' ', $title) ?? $title;

        return trim($title);
    }
}
