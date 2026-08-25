<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class SplitChapterCandidateAction
{
    use AsAction;

    public function rules(): array
    {
        return [
            'split_page' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array{first: ChapterCandidate, second: ChapterCandidate}
     */
    public function handle(ChapterCandidate $candidate, int $splitPage): array
    {
        $oldEnd = $candidate->end_page;
        $candidate->update([
            'end_page' => max($candidate->start_page, $splitPage - 1),
        ]);

        $nextSeq = $candidate->sequence + 1;

        // Shift subsequent sequences up by 1
        $candidate->documentImport->candidates()
            ->where('sequence', '>=', $nextSeq)
            ->increment('sequence');

        $text = $candidate->source_text ?? '';
        $halfLen = (int) (strlen($text) / 2);
        $firstText = substr($text, 0, $halfLen);
        $secondText = substr($text, $halfLen);

        $candidate->update([
            'source_text' => $firstText,
            'word_count' => str_word_count($firstText),
            'content_hash' => hash('sha256', $firstText),
        ]);

        $newCandidate = ChapterCandidate::create([
            'document_import_id' => $candidate->document_import_id,
            'sequence' => $nextSeq,
            'detected_number' => ($candidate->detected_number ?? 0) + 1,
            'resolved_chapter_number' => ($candidate->resolved_chapter_number ?? 0) + 1,
            'detected_title' => "{$candidate->title} (Part 2)",
            'title' => "{$candidate->title} (Part 2)",
            'start_page' => $splitPage,
            'end_page' => max($splitPage, $oldEnd),
            'word_count' => str_word_count($secondText),
            'confidence_score' => $candidate->confidence_score,
            'confidence_level' => $candidate->confidence_level,
            'status' => ChapterCandidateStatus::REVIEW_REQUIRED,
            'source_text' => $secondText,
            'content_hash' => hash('sha256', $secondText),
        ]);

        return [
            'first' => $candidate,
            'second' => $newCandidate,
        ];
    }

    public function asController(ChapterCandidate $candidate, Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $this->handle($candidate, (int) $validated['split_page']);

        return back()->with('success', 'Candidate split successfully into two chapters.');
    }
}
