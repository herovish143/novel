<?php

namespace Domain\Novel\Actions;

use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Novel\Services\PdfChapterExtractor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class ExtractPdfChaptersAction
{
    use AsAction;

    public function __construct(
        protected PdfChapterExtractor $extractor = new PdfChapterExtractor
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ];
    }

    /**
     * @return Collection<int, Chapter>
     */
    public function handle(Novel $novel, string $pdfFilePath): Collection
    {
        $chaptersData = $this->extractor->extractFromPath($pdfFilePath);
        $importedChapters = collect();

        $importAction = app(ImportChapterAction::class);

        foreach ($chaptersData as $ch) {
            $chapter = $importAction->handle(
                novel: $novel,
                chapterNumber: $ch['chapter_number'],
                title: $ch['title'],
                sourceText: $ch['source_text'],
                sourceUrl: null
            );

            $importedChapters->push($chapter);
        }

        return $importedChapters;
    }

    public function asController(Novel $novel, Request $request): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('pdf_file');

        $imported = $this->handle($novel, $file->getRealPath());

        return to_route('novels.show', $novel->id)
            ->with('success', "Successfully extracted and imported {$imported->count()} chapters from PDF.");
    }
}
