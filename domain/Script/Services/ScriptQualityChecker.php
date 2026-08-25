<?php

namespace Domain\Script\Services;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;
use Domain\StoryMemory\Models\ChapterFact;

class ScriptQualityChecker
{
    /**
     * Run automated quality and compliance checks on a Hindi script draft.
     *
     * @return array{passed: bool, warnings: list<string>, estimated_duration_sec: int}
     */
    public function check(Script $script, Chapter $chapter): array
    {
        $warnings = [];

        // 1. Estimated duration check (Hindi speech avg 12-15 chars per sec)
        $charCount = mb_strlen($script->full_script ?: '');
        $estimatedSec = (int) ceil($charCount / 14);

        if ($charCount < 100) {
            $warnings[] = 'Script length is unusually short (under 100 characters).';
        }

        // 2. Fact coverage check
        $factsCount = ChapterFact::where('chapter_id', $chapter->id)->count();
        if ($factsCount > 0 && $charCount > 0) {
            $facts = ChapterFact::where('chapter_id', $chapter->id)->get();
            foreach ($facts as $fact) {
                // Simple keyword presence warning
                $keywords = array_filter(explode(' ', $fact->statement), fn ($w): bool => mb_strlen($w) > 4);
                $found = false;
                foreach ($keywords as $kw) {
                    if (mb_stripos($script->full_script, $kw) !== false) {
                        $found = true;
                        break;
                    }
                }
                if (! $found && count($keywords) > 0) {
                    $warnings[] = "Fact coverage warning: Fact '{$fact->statement}' keywords may be missing from Hindi narration.";
                }
            }
        }

        // 3. Hallucination check flag
        $passed = count(array_filter($warnings, fn (string $w): bool => str_contains($w, 'unusually short'))) === 0;

        return [
            'passed' => $passed,
            'warnings' => array_values(array_unique($warnings)),
            'estimated_duration_sec' => $estimatedSec,
        ];
    }
}
