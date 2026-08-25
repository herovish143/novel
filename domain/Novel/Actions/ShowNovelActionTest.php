<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Models\Novel;

test('show novel action displays novel details with chapters and characters', function () {
    $user = User::factory()->create();
    $novel = Novel::factory()->create();

    $response = $this->actingAs($user)->get(route('novels.show', $novel->id));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Novels/Show')
            ->has('novel')
            ->has('chapters')
            ->has('characters')
        );
});
