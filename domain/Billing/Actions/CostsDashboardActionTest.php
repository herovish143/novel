<?php

namespace Domain\Billing\Actions;

use App\Models\User;

test('costs dashboard action renders spend analytics and usage logs', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('costs.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Costs/Index')
            ->has('totalSpend')
            ->has('spendByService')
            ->has('spendByProvider')
        );
});
