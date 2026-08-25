<?php

namespace Domain\Novel\Actions;

use App\Models\User;
use Domain\Novel\Models\Novel;

test('list novels action returns paginated list of novels', function () {
    $user = User::factory()->create();
    Novel::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('novels.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Novels/Index')
            ->has('novels.data', 3)
        );
});
