<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;

test('show chapter action loads chapter workspace with source versions and facts', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id]);

    $response = $this->actingAs($user)->get(route('chapters.show', $chapter->id));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Chapters/Show')
            ->has('chapter')
            ->has('sourceVersions')
            ->has('facts')
        );
});
