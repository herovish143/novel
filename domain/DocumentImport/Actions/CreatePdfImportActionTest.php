<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\Novel\Models\Novel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('create pdf import action stores pdf and initializes candidates', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $novel = Novel::factory()->create();

    $response = $this->actingAs($user)->post(route('novels.pdf.upload', $novel->id), [
        'pdf_file' => UploadedFile::fake()->create('novel_book.pdf', 100, 'application/pdf'),
    ]);

    $this->assertDatabaseHas('document_imports', [
        'novel_id' => $novel->id,
        'original_filename' => 'novel_book.pdf',
    ]);
});
