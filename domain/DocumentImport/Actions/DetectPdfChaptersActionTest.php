<?php

namespace Domain\DocumentImport\Actions;

use Domain\DocumentImport\Models\DocumentImport;
use Domain\Novel\Services\PdfChapterExtractor;
use Mockery;

test('detect pdf chapters action populates candidates with confidence scores', function () {
    $import = DocumentImport::factory()->create();

    $mockExtractor = Mockery::mock(PdfChapterExtractor::class);
    $mockExtractor->shouldReceive('extractFromPath')
        ->withAnyArgs()
        ->andReturn([
            [
                'chapter_number' => 1,
                'title' => 'Chapter 1: The Awakening',
                'source_text' => 'Full narrative text for chapter 1 in test environment.',
                'word_count' => 8,
            ],
        ]);

    app()->instance(PdfChapterExtractor::class, $mockExtractor);

    $action = app(DetectPdfChaptersAction::class);
    $candidates = $action->handle($import);

    expect($candidates)->not->toBeEmpty();
    expect($candidates->first()->confidence_score)->toBeGreaterThanOrEqual(10);
    expect($import->fresh()->status->value)->toBe('REVIEW_REQUIRED');
});
