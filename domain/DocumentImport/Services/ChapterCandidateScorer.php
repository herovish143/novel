<?php

namespace Domain\DocumentImport\Services;

class ChapterCandidateScorer
{
    /**
     * @return array{score: int, level: string}
     */
    public function calculate(string $title, int $wordCount, int $sequence, ?int $detectedNumber): array
    {
        $score = 0;

        // Signal 1: Heading pattern match (contains "Chapter", "Ch.", "Episode", "अध्याय")
        if (preg_match('/(?:Chapter|CHAPTER|Ch\.|Episode|अध्याय)\s*\d+/i', $title)) {
            $score += 35;
        } elseif (strlen($title) > 0 && strlen($title) < 70) {
            $score += 20;
        }

        // Signal 2: Sequence alignment
        if ($detectedNumber !== null && $detectedNumber > 0) {
            $score += 25;
        } else {
            $score += 15;
        }

        // Signal 3: Chapter Word Count reasonableness (>250 words)
        if ($wordCount >= 300) {
            $score += 25;
        } elseif ($wordCount >= 100) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Signal 4: Formatting cleanliness
        if (! str_contains($title, "\n") && strlen($title) <= 80) {
            $score += 15;
        }

        $score = min(100, max(10, $score));

        $level = match (true) {
            $score >= 85 => 'HIGH',
            $score >= 60 => 'MEDIUM',
            default => 'LOW',
        };

        return [
            'score' => $score,
            'level' => $level,
        ];
    }
}
