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

    public function handle(): \Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Illuminate\Support\Enumerable|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Contracts\Pagination\CursorPaginator|array
    {
        $novels = Novel::query()
            ->withCount(['chapters', 'characters', 'locations'])
            ->latest('updated_at')
            ->get();

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
