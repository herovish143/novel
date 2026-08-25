<?php

namespace Domain\Novel\Actions;

use Domain\Novel\Data\CreateNovelData;
use Domain\Novel\Models\Novel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNovelAction
{
    use AsAction;

    public function handle(CreateNovelData $data): Novel
    {
        $attributes = $data->toArray();
        $attributes['slug'] = Str::slug($data->title).'-'.rand(100, 999);

        return Novel::create($attributes);
    }

    public function asController(CreateNovelData $data): RedirectResponse
    {
        $this->handle($data);

        return to_route('novels.index')->with('success', 'Novel created successfully.');
    }
}
