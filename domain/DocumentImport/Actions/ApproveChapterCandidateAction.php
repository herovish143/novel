<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class ApproveChapterCandidateAction
{
    use AsAction;

    public function handle(ChapterCandidate $candidate): ChapterCandidate
    {
        $candidate->update([
            'status' => ChapterCandidateStatus::APPROVED,
            'approved_at' => now(),
        ]);

        // Update counts on DocumentImport
        $import = $candidate->documentImport;
        $import->update([
            'approved_chapters_count' => $import->candidates()->where('status', ChapterCandidateStatus::APPROVED)->count(),
        ]);

        return $candidate;
    }

    public function asController(ChapterCandidate $candidate): RedirectResponse
    {
        $this->handle($candidate);

        return back()->with('success', 'Candidate approved.');
    }
}
