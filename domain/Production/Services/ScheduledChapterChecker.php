<?php

namespace Domain\Production\Services;

use Domain\Novel\Actions\ImportChapterAction;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;

class ScheduledChapterChecker
{
    public function __construct(
        protected ImportChapterAction $importAction
    ) {}

    /**
     * Check all active novels for new chapter releases.
     *
     * @return int Number of newly imported chapters
     */
    public function checkAll(): int
    {
        $novels = Novel::where('status', 'ACTIVE')->get();
        $importedCount = 0;

        foreach ($novels as $novel) {
            $latestChapterNumber = Chapter::where('novel_id', $novel->id)->max('chapter_number') ?: 0;
            $nextChapterNumber = $latestChapterNumber + 1;

            // In production, fetch source URL feed/scraper. Here we provide a mock automated ingestion fallback.
            if ($novel->source_url) {
                $sampleText = "Sunny faced the next trial in Chapter {$nextChapterNumber}. Shadows surrounded the arena as he prepared his aspect.";
                $this->importAction->handle(
                    novel: $novel,
                    chapterNumber: $nextChapterNumber,
                    title: "Automated Release Chapter {$nextChapterNumber}",
                    sourceText: $sampleText,
                    sourceUrl: "{$novel->source_url}/chapter-{$nextChapterNumber}"
                );
                $importedCount++;
            }
        }

        return $importedCount;
    }
}
