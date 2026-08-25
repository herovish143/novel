<?php

namespace Domain\StoryMemory\Services;

use Domain\Novel\Models\Chapter;

class StoryContextBuilder
{
    /**
     * Build context string from persistent story memory for prompt injection.
     */
    public function buildContext(Chapter $chapter): string
    {
        $novel = $chapter->novel;

        // 1. Previous Chapter Summary
        $previousSummaryText = 'None (This is the first chapter).';
        if ($chapter->previous_chapter_id) {
            $prevChapter = Chapter::with('summary')->find($chapter->previous_chapter_id);
            if ($prevChapter?->summary) {
                $previousSummaryText = "Chapter {$prevChapter->chapter_number} ({$prevChapter->title}): {$prevChapter->summary->summary}";
            }
        }

        // 2. Main & Recent Characters
        $characters = $novel->characters()
            ->with('aliases')
            ->where(function ($q) use ($chapter): void {
                $q->where('importance', 'MAIN')
                    ->orWhere('last_seen_chapter_id', $chapter->previous_chapter_id)
                    ->orWhere('last_seen_chapter_id', $chapter->id);
            })
            ->limit(15)
            ->get();

        $characterList = $characters->map(function ($c): string {
            $aliases = $c->aliases->pluck('alias')->implode(', ');
            $aliasText = $aliases !== '' ? " (Aliases: {$aliases})" : '';

            return "- {$c->canonical_name}{$aliasText}: {$c->physical_description} Personality: {$c->personality}";
        })->implode("\n");

        if ($characterList === '') {
            $characterList = 'No prior character data.';
        }

        // 3. Locations
        $locations = $novel->locations()->limit(10)->get();
        $locationList = $locations->map(fn ($l): string => "- {$l->name}: {$l->description}")->implode("\n");
        if ($locationList === '') {
            $locationList = 'No prior location data.';
        }

        // 4. Open Questions from previous summaries
        $openQuestions = 'None.';
        if ($chapter->previous_chapter_id) {
            $prevSummary = Chapter::find($chapter->previous_chapter_id)?->summary;
            if ($prevSummary && ! empty($prevSummary->unresolved_questions)) {
                $openQuestions = implode("\n", array_map(fn ($q): string => "- {$q}", $prevSummary->unresolved_questions));
            }
        }

        return <<<CONTEXT
=== NOVEL INFORMATION ===
Title: {$novel->title}
Visual Style: {$novel->visual_style}
Narration Style: {$novel->narration_style}

=== PREVIOUS CHAPTER SUMMARY ===
{$previousSummaryText}

=== KNOWN CHARACTERS ===
{$characterList}

=== KNOWN LOCATIONS ===
{$locationList}

=== OPEN STORY QUESTIONS ===
{$openQuestions}
CONTEXT;
    }
}
