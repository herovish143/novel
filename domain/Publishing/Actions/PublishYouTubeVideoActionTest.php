<?php

namespace Domain\Publishing\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Publishing\Services\FakeYouTubeService;
use Domain\Publishing\Services\YouTubeService;
use Domain\Shared\Services\Ai\FakeLanguageModel;
use Domain\Shared\Services\Ai\LanguageModel;
use Domain\Visual\Services\FakeImageGenerator;
use Domain\Visual\Services\ImageGenerator;

test('publish youtube video action checks rights status and uploads draft', function () {
    app()->bind(YouTubeService::class, FakeYouTubeService::class);
    app()->bind(LanguageModel::class, FakeLanguageModel::class);
    app()->bind(ImageGenerator::class, FakeImageGenerator::class);

    $user = User::factory()->create();
    $novel = Novel::factory()->create(['rights_status' => 'OWNED']);
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);

    $response = $this->actingAs($user)->post(route('youtube.publish', $chapter->id));

    $response->assertRedirect(route('chapters.show', $chapter->id));
});
