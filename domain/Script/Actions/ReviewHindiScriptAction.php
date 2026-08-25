<?php

namespace Domain\Script\Actions;

use Domain\Novel\Data\ChapterData;
use Domain\Novel\Data\NovelData;
use Domain\Script\Data\ScriptData;
use Domain\Script\Data\ScriptSegmentData;
use Domain\Script\Models\Script;
use Domain\StoryMemory\Data\ChapterSummaryData;
use Domain\StoryMemory\Data\StoryEventData;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ReviewHindiScriptAction
{
    use AsAction;

    public function handle(Script $script): array
    {
        $script->load(['chapter.novel', 'chapter.summary', 'chapter.events', 'segments']);

        return [
            'script' => ScriptData::from($script),
            'chapter' => ChapterData::from($script->chapter),
            'novel' => NovelData::from($script->chapter->novel),
            'summary' => $script->chapter->summary ? ChapterSummaryData::from($script->chapter->summary) : null,
            'events' => StoryEventData::collect($script->chapter->events),
            'segments' => ScriptSegmentData::collect($script->segments),
        ];
    }

    public function asController(Script $script): Response
    {
        $data = $this->handle($script);

        return Inertia::render('Scripts/Review', $data);
    }
}
