<?php

namespace Domain\Novel\Actions;

use Domain\Novel\Data\ChapterData;
use Domain\Novel\Data\NovelData;
use Domain\Novel\Models\Novel;
use Domain\StoryMemory\Data\CharacterData;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowNovelAction
{
    use AsAction;

    public function handle(Novel $novel): array
    {
        $novel->loadCount(['chapters', 'characters', 'locations']);

        $chapters = $novel->chapters()
            ->with(['summary', 'latestScript'])
            ->orderBy('chapter_number')
            ->get();

        $characters = $novel->characters()
            ->with('aliases')
            ->orderBy('canonical_name')
            ->get();

        return [
            'novel' => NovelData::from($novel),
            'chapters' => ChapterData::collect($chapters),
            'characters' => CharacterData::collect($characters),
        ];
    }

    public function asController(Novel $novel): Response
    {
        $data = $this->handle($novel);

        return Inertia::render('Novels/Show', $data);
    }
}
