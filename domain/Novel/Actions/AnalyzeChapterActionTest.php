<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Shared\Services\Ai\FakeLanguageModel;
use Domain\Shared\Services\Ai\LanguageModel;

test('analyze chapter action extracts facts and updates chapter status', function () {
    app()->bind(LanguageModel::class, FakeLanguageModel::class);

    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);

    $response = $this->actingAs($user)->post(route('chapters.analyze', $chapter->id));

    $response->assertRedirect(route('chapters.show', $chapter->id));

    $this->assertDatabaseHas('chapters', [
        'id' => $chapter->id,
        'status' => 'MEMORY_UPDATED',
    ]);
});
