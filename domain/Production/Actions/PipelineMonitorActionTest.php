<?php

namespace Domain\Production\Actions;

use App\Models\User;

test('pipeline monitor action renders operations dashboard with queue health', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('pipeline.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Pipeline/Index')
            ->has('runs')
            ->has('queues')
        );
});
