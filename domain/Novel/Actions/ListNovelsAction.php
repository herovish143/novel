<?php

namespace Domain\Novel\Actions;

use Domain\Novel\Data\NovelData;
use Domain\Novel\Models\Novel;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ListNovelsAction
{
    use AsAction;

    public function handle()
    {
        $novels = Novel::query()
            ->withCount(['chapters', 'characters', 'locations'])
            ->latest('updated_at')
            ->paginate(15);

        return NovelData::collect($novels);
    }

    public function asController(): Response
    {
        $novelsData = $this->handle();

        return Inertia::render('Novels/Index', [
            'novels' => $novelsData,
        ]);
    }
}
