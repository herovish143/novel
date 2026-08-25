<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Models\Novel;
use Domain\Novel\Services\PdfChapterExtractor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;

test('extract pdf chapters action parses pdf text and bulk imports chapters', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $novel = Novel::factory()->create();

    // Test extracting text directly via extractor
    $extractor = new PdfChapterExtractor;
    $text = "Chapter 1: The Awakening\nThis is raw story text for chapter 1.\n\nChapter 2: The Return\nThis is raw story text for chapter 2.";

    $extracted = $extractor->extractFromText($text);

    expect($extracted)->toHaveCount(2);
    expect($extracted[0]['title'])->toContain('Chapter 1');
    expect($extracted[1]['title'])->toContain('Chapter 2');

    // Mock extractor for HTTP file upload test
    $mockExtractor = Mockery::mock(PdfChapterExtractor::class);
    $mockExtractor->shouldReceive('extractFromPath')
        ->once()
        ->andReturn([
            [
                'chapter_number' => 1,
                'title' => 'Chapter 1: The Awakening',
                'source_text' => 'This is raw story text for chapter 1.',
                'word_count' => 8,
            ],
        ]);

    app()->instance(PdfChapterExtractor::class, $mockExtractor);

    // Test controller endpoint validation
    $response = $this->actingAs($user)->post(route('novels.pdf.import', $novel->id), [
        'pdf_file' => UploadedFile::fake()->create('novel.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('novels.show', $novel->id));

    $this->assertDatabaseHas('chapters', [
        'novel_id' => $novel->id,
        'chapter_number' => 1,
        'title' => 'Chapter 1: The Awakening',
    ]);
});
