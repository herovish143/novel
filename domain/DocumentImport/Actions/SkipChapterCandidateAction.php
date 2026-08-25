<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class SkipChapterCandidateAction
{
    use AsAction;

    public function handle(ChapterCandidate $candidate): ChapterCandidate
    {
        $candidate->update([
            'status' => ChapterCandidateStatus::SKIPPED,
        ]);

        $import = $candidate->documentImport;
        $import->update([
            'skipped_chapters_count' => $import->candidates()->where('status', ChapterCandidateStatus::SKIPPED)->count(),
        ]);

        return $candidate;
    }

    public function asController(ChapterCandidate $candidate): RedirectResponse
    {
        $this->handle($candidate);

        return back()->with('success', 'Candidate marked as skipped.');
    }
}
