<?php

namespace Domain\Novel\Actions;

use Domain\Novel\Data\ImportChapterData;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\ChapterSourceVersion;
use Domain\Novel\Models\Novel;
use Domain\Production\Models\ProductionRun;
use Domain\Production\Models\ProductionStep;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportChapterAction
{
    use AsAction;

    public function handle(
        Novel $novel,
        int $chapterNumber,
        string $title,
        string $sourceText,
        ?string $sourceUrl = null
    ): Chapter {
        $cleanedText = $this->cleanText($sourceText);

        if (trim($cleanedText) === '') {
            throw new InvalidArgumentException('Source text cannot be empty after normalization.');
        }

        $sourceHash = hash('sha256', $cleanedText);

        $previousChapter = Chapter::query()
            ->where('novel_id', $novel->id)
            ->where('chapter_number', '<', $chapterNumber)
            ->orderByDesc('chapter_number')
            ->first();

        $chapter = Chapter::updateOrCreate(
            [
                'novel_id' => $novel->id,
                'chapter_number' => $chapterNumber,
            ],
            [
                'title' => trim($title),
                'source_url' => $sourceUrl,
                'source_text' => $cleanedText,
                'source_hash' => $sourceHash,
                'previous_chapter_id' => $previousChapter?->id,
                'status' => 'IMPORTED',
                'imported_at' => now(),
            ]
        );

        $latestVersion = ChapterSourceVersion::where('chapter_id', $chapter->id)->max('version') ?: 0;
        ChapterSourceVersion::create([
            'chapter_id' => $chapter->id,
            'version' => $latestVersion + 1,
            'raw_content' => $sourceText,
            'clean_content' => $cleanedText,
            'content_hash' => $sourceHash,
            'import_method' => $sourceUrl ? 'URL' : 'MANUAL',
            'imported_by' => auth()->user()?->name ?? 'System',
            'created_at' => now(),
        ]);

        $productionRun = ProductionRun::create([
            'chapter_id' => $chapter->id,
            'status' => 'IMPORTED',
            'current_stage' => 'IMPORTED',
            'started_at' => now(),
        ]);

        ProductionStep::create([
            'production_run_id' => $productionRun->id,
            'stage' => 'ANALYZE_CHAPTER',
            'status' => 'PENDING',
        ]);

        return $chapter;
    }

    public function asController(Novel $novel, ImportChapterData $data): RedirectResponse
    {
        $chapter = $this->handle(
            novel: $novel,
            chapterNumber: $data->chapter_number,
            title: $data->title,
            sourceText: $data->source_text,
            sourceUrl: $data->source_url
        );

        return to_route('chapters.show', $chapter->id)->with('success', 'Chapter imported successfully.');
    }

    protected function cleanText(string $text): string
    {
        $clean = strip_tags($text);
        $clean = (string) preg_replace("/[\r\n]{3,}/", "\n\n", $clean);

        return trim($clean);
    }
}
