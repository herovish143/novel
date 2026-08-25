<?php

namespace Domain\Production\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Shared\Services\Ai\FakeLanguageModel;
use Domain\Shared\Services\Ai\LanguageModel;

test('start production action launches pipeline orchestrator run', function () {
    app()->bind(LanguageModel::class, FakeLanguageModel::class);

    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);

    $response = $this->actingAs($user)->post(route('production.start', $chapter->id));

    $response->assertRedirect(route('chapters.show', $chapter->id));
});
