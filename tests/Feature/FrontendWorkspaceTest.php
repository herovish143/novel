<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_queue_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/reviews');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Reviews/Index')
            ->has('scripts')
            ->has('audio')
            ->has('visuals')
            ->has('videos')
        );
    }

    public function test_pipeline_monitor_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/pipeline');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Pipeline/Index')
            ->has('runs')
            ->has('queues')
        );
    }

    public function test_costs_dashboard_page_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/costs');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Costs/Index')
            ->has('totalSpend')
            ->has('spendByService')
            ->has('spendByProvider')
        );
    }
}
