<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Enums\DocumentImportStatus;
use Domain\DocumentImport\Enums\ExtractionMethod;
use Domain\DocumentImport\Models\DocumentImport;
use Domain\Novel\Models\Novel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePdfImportAction
{
    use AsAction;

    public function rules(): array
    {
        return [
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ];
    }

    public function handle(Novel $novel, UploadedFile $file): DocumentImport
    {
        $sha256 = hash_file('sha256', $file->getRealPath());

        // Check if duplicate PDF import exists for this novel
        $existing = DocumentImport::where('novel_id', $novel->id)
            ->where('sha256', $sha256)
            ->first();

        if ($existing) {
            return $existing;
        }

        $storagePath = $file->storeAs(
            "novels/{$novel->id}/imports",
            uniqid('pdf_').'.pdf',
            'public'
        );

        $documentImport = DocumentImport::create([
            'novel_id' => $novel->id,
            'original_filename' => $file->getClientOriginalName(),
            'storage_disk' => 'public',
            'storage_path' => $storagePath,
            'mime_type' => $file->getClientMimeType() ?: 'application/pdf',
            'file_size' => $file->getSize(),
            'sha256' => $sha256,
            'page_count' => 0,
            'status' => DocumentImportStatus::UPLOADED,
            'extraction_method' => ExtractionMethod::NATIVE,
        ]);

        // Automatically trigger chapter detection
        app(DetectPdfChaptersAction::class)->handle($documentImport);

        return $documentImport;
    }

    public function asController(Novel $novel, Request $request): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('pdf_file');

        $documentImport = $this->handle($novel, $file);

        return to_route('document-imports.show', $documentImport->id)
            ->with('success', 'PDF uploaded successfully. Review chapter candidates below.');
    }
}
