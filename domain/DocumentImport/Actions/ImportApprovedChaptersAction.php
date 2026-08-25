<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Enums\DocumentImportStatus;
use Domain\DocumentImport\Models\DocumentImport;
use Domain\Novel\Actions\ImportChapterAction;
use Domain\Novel\Models\Chapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportApprovedChaptersAction
{
    use AsAction;

    /**
     * @return Collection<int, Chapter>
     */
    public function handle(DocumentImport $documentImport): Collection
    {
        $approvedCandidates = $documentImport->candidates()
            ->where('status', ChapterCandidateStatus::APPROVED)
            ->get();

        $importedChapters = collect();
        $importChapterAction = app(ImportChapterAction::class);

        foreach ($approvedCandidates as $candidate) {
            DB::transaction(function () use ($documentImport, $candidate, $importChapterAction, $importedChapters) {
                // Check if chapter with same content or number exists for this novel
                $existingChapter = $documentImport->novel->chapters()
                    ->where('chapter_number', $candidate->resolved_chapter_number ?? $candidate->sequence)
                    ->first();

                if ($existingChapter) {
                    $candidate->update([
                        'status' => ChapterCandidateStatus::DUPLICATE,
                        'chapter_id' => $existingChapter->id,
                    ]);

                    return;
                }

                $chapter = $importChapterAction->handle(
                    novel: $documentImport->novel,
                    chapterNumber: $candidate->resolved_chapter_number ?? $candidate->sequence,
                    title: $candidate->title ?? "Chapter {$candidate->sequence}",
                    sourceText: $candidate->source_text ?? '',
                    sourceUrl: null
                );

                $candidate->update([
                    'status' => ChapterCandidateStatus::IMPORTED,
                    'chapter_id' => $chapter->id,
                    'imported_at' => now(),
                ]);

                $importedChapters->push($chapter);
            });
        }

        $documentImport->update([
            'imported_chapters_count' => $documentImport->candidates()->where('status', ChapterCandidateStatus::IMPORTED)->count(),
            'status' => DocumentImportStatus::COMPLETED,
        ]);

        return $importedChapters;
    }

    public function asController(DocumentImport $documentImport): RedirectResponse
    {
        $imported = $this->handle($documentImport);

        return to_route('novels.show', $documentImport->novel_id)
            ->with('success', "Successfully imported {$imported->count()} approved chapters.");
    }
}
