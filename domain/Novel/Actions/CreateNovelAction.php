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
        $attributes = collect($data->toArray())
            ->except(['pdf_file', 'pdfFile'])
            ->toArray();

        $attributes['slug'] = Str::slug($data->title).'-'.rand(100, 999);

        $novel = Novel::create($attributes);

        if ($data->pdfFile) {
            app(ExtractPdfChaptersAction::class)->handle($novel, $data->pdfFile->getRealPath());
        }

        return $novel;
    }

    public function asController(CreateNovelData $data): RedirectResponse
    {
        $novel = $this->handle($data);

        $message = $data->pdfFile
            ? 'Novel created and PDF chapters extracted successfully.'
            : 'Novel created successfully.';

        return to_route('novels.show', $novel->id)->with('success', $message);
    }
}
