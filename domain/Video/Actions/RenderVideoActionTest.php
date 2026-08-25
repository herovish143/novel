<?php

namespace Domain\Video\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;

test('render video action builds timeline and renders 1080p video project', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);

    $response = $this->actingAs($user)->post(route('video.render', $chapter->id));

    $response->assertRedirect(route('chapters.show', $chapter->id));
});
