<?php

namespace Domain\Publishing\Actions;

use App\Models\User;

test('review queue action renders review queue workspace with paginated tabs', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('reviews.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Reviews/Index')
            ->has('scripts')
            ->has('audio')
            ->has('visuals')
            ->has('videos')
        );
});
