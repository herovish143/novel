<?php

namespace Domain\Script\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Shared\Services\Ai\FakeLanguageModel;
use Domain\Shared\Services\Ai\LanguageModel;

test('generate hindi script action generates script and redirects to review', function () {
    app()->bind(LanguageModel::class, FakeLanguageModel::class);

    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'status' => 'ANALYZED']);

    $response = $this->actingAs($user)->post(route('scripts.generate', $chapter->id));

    $this->assertDatabaseHas('scripts', [
        'chapter_id' => $chapter->id,
        'status' => 'NEEDS_REVIEW',
    ]);
});
