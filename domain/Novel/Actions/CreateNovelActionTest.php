<?php

namespace Domain\Novel\Actions;

use App\Models\User;

test('create novel action creates a novel and redirects', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('novels.store'), [
        'title' => 'Solo Leveling Hindi',
        'original_language' => 'ko',
        'output_language' => 'hi',
        'source_url' => 'https://example.com/novel',
        'description' => 'A hunter acquires legendary power.',
        'visual_style' => 'anime',
        'narration_style' => 'dramatic',
        'max_cost_per_episode' => 500.00,
    ]);

    $this->assertDatabaseHas('novels', [
        'title' => 'Solo Leveling Hindi',
    ]);
});
