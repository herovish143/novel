<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Data\ChapterCandidateData;
use Domain\DocumentImport\Data\DocumentImportData;
use Domain\DocumentImport\Models\DocumentImport;
use Domain\Novel\Data\NovelData;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowDocumentImportAction
{
    use AsAction;

    public function handle(DocumentImport $documentImport): Response
    {
        $documentImport->load(['novel', 'candidates']);

        return Inertia::render('DocumentImports/Show', [
            'documentImport' => DocumentImportData::fromModel($documentImport),
            'novel' => NovelData::fromModel($documentImport->novel),
            'candidates' => ChapterCandidateData::collect($documentImport->candidates),
        ]);
    }
}
