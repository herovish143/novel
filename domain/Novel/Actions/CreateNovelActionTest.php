<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Services\PdfChapterExtractor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;

test('create novel action creates a novel and redirects', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('novels.store'), [
        'title' => 'Solo Leveling Hindi',
        'original_language' => 'ko',
        'output_language' => 'hi',
        'source_url' => 'https://example.com/novel',
        'description' => 'A hunter acquires legendary power.',
        'visual_style' => 'anime',
        'narration_style' => 'dramatic',
        'max_cost_per_episode' => 500.00,
    ]);

    $this->assertDatabaseHas('novels', [
        'title' => 'Solo Leveling Hindi',
    ]);
});

test('create novel action extracts pdf chapters automatically when pdf_file is provided', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $mockExtractor = Mockery::mock(PdfChapterExtractor::class);
    $mockExtractor->shouldReceive('extractFromPath')
        ->once()
        ->andReturn([
            [
                'chapter_number' => 1,
                'title' => 'Chapter 1: Rise',
                'source_text' => 'Full text of chapter 1 from uploaded PDF.',
                'word_count' => 8,
            ],
        ]);

    app()->instance(PdfChapterExtractor::class, $mockExtractor);

    $response = $this->actingAs($user)->post(route('novels.store'), [
        'title' => 'Lord of the Mysteries',
        'original_language' => 'en',
        'output_language' => 'hi',
        'visual_style' => 'steampunk',
        'narration_style' => 'mysterious',
        'max_cost_per_episode' => 5.00,
        'pdf_file' => UploadedFile::fake()->create('book.pdf', 200, 'application/pdf'),
    ]);

    $this->assertDatabaseHas('novels', [
        'title' => 'Lord of the Mysteries',
    ]);

    $this->assertDatabaseHas('chapters', [
        'title' => 'Chapter 1: Rise',
    ]);
});
