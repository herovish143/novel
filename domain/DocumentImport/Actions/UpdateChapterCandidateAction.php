<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Models\ChapterCandidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateChapterCandidateAction
{
    use AsAction;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'resolved_chapter_number' => ['required', 'integer', 'min:1'],
        ];
    }

    public function handle(ChapterCandidate $candidate, string $title, int $resolvedNumber): ChapterCandidate
    {
        $candidate->update([
            'title' => $title,
            'resolved_chapter_number' => $resolvedNumber,
        ]);

        return $candidate;
    }

    public function asController(ChapterCandidate $candidate, Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $this->handle(
            $candidate,
            $validated['title'],
            (int) $validated['resolved_chapter_number']
        );

        return back()->with('success', 'Candidate updated successfully.');
    }
}
