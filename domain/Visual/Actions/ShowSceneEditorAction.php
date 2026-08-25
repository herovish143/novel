<?php

namespace Domain\Visual\Actions;

use Domain\Novel\Data\ChapterData;
use Domain\Novel\Data\NovelData;
use Domain\Novel\Models\Chapter;
use Domain\Visual\Data\SceneData;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowSceneEditorAction
{
    use AsAction;

    public function handle(Chapter $chapter): array
    {
        $chapter->load(['novel', 'scenes.assets']);

        $scenes = $chapter->scenes()->orderBy('sequence')->get();

        return [
            'chapter' => ChapterData::from($chapter),
            'novel' => NovelData::from($chapter->novel),
            'scenes' => SceneData::collect($scenes),
        ];
    }

    public function asController(Chapter $chapter): Response
    {
        $data = $this->handle($chapter);

        return Inertia::render('Scenes/Editor', $data);
    }
}
