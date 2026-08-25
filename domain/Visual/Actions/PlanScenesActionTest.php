<?php

namespace Domain\Visual\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;

test('plan scenes action creates visual scenes and redirects to editor', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);

    $response = $this->actingAs($user)->post(route('scenes.plan', $chapter->id));

    $response->assertRedirect(route('scenes.editor', $chapter->id));
});
