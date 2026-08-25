<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Models\Novel;

test('import chapter action imports chapter and creates source version snapshot', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();

    $response = $this->actingAs($user)->post(route('chapters.store', $novel->id), [
        'chapter_number' => 1,
        'title' => 'Chapter 1: Awakening',
        'source_text' => 'The world changed when dungeons appeared everywhere.',
    ]);

    $this->assertDatabaseHas('chapters', [
        'novel_id' => $novel->id,
        'chapter_number' => 1,
        'title' => 'Chapter 1: Awakening',
    ]);

    $this->assertDatabaseHas('chapter_source_versions', [
        'version' => 1,
    ]);
});
