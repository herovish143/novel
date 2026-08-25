<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Models\ChapterCandidate;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class MergeChapterCandidatesAction
{
    use AsAction;

    public function handle(ChapterCandidate $candidate): ChapterCandidate
    {
        $nextCandidate = ChapterCandidate::where('document_import_id', $candidate->document_import_id)
            ->where('sequence', $candidate->sequence + 1)
            ->first();

        if (! $nextCandidate) {
            return $candidate;
        }

        $mergedText = trim(($candidate->source_text ?? '')."\n\n".($nextCandidate->source_text ?? ''));

        $candidate->update([
            'end_page' => $nextCandidate->end_page,
            'source_text' => $mergedText,
            'word_count' => str_word_count($mergedText),
            'content_hash' => hash('sha256', $mergedText),
        ]);

        $nextCandidate->delete();

        // Re-index remaining candidate sequences
        $candidate->documentImport->candidates()
            ->where('sequence', '>', $candidate->sequence)
            ->decrement('sequence');

        return $candidate;
    }

    public function asController(ChapterCandidate $candidate): RedirectResponse
    {
        $this->handle($candidate);

        return back()->with('success', 'Candidate merged with next chapter successfully.');
    }
}
