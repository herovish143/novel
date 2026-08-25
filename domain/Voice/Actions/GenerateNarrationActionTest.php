<?php

namespace Domain\Voice\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Script\Models\Script;

test('generate narration action synthesizes speech audio segments and subtitles', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);
    $script = Script::create([
        'chapter_id' => $chapter->id,
        'version' => 1,
        'full_script' => 'सोल लेवलिंग हिंदी!',
        'status' => 'APPROVED',
        'word_count' => 10,
    ]);

    $response = $this->actingAs($user)->post(route('scripts.audio.generate', $script->id));

    $response->assertRedirect(route('chapters.show', $chapter->id));
});
