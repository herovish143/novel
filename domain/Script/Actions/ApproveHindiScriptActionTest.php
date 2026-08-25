<?php

namespace Domain\Script\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Script\Models\Script;

test('approve hindi script action sets script status to APPROVED', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);
    $script = Script::create([
        'chapter_id' => $chapter->id,
        'version' => 1,
        'full_script' => 'नमस्कार दोस्तों!',
        'status' => 'GENERATED',
        'word_count' => 50,
    ]);

    $response = $this->actingAs($user)->post(route('scripts.approve', $script->id));

    $response->assertRedirect(route('chapters.show', $chapter->id));
    $this->assertDatabaseHas('scripts', [
        'id' => $script->id,
        'status' => 'APPROVED',
    ]);
});
