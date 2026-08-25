<?php

namespace Domain\Billing\Actions;

use Domain\Billing\Models\AiUsage;
use Domain\Novel\Models\Novel;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class CostsDashboardAction
{
    use AsAction;

    public function handle(): array
    {
        $totalSpend = (float) AiUsage::sum('estimated_cost');

        $spendByService = AiUsage::query()
            ->selectRaw('service, SUM(estimated_cost) as total_cost, COUNT(*) as count')
            ->groupBy('service')
            ->get()
            ->map(fn ($item): array => [
                'service' => $item->service,
                'total_cost' => round((float) $item->total_cost, 4),
                'count' => (int) $item->count,
            ]);

        $spendByProvider = AiUsage::query()
            ->selectRaw('provider, SUM(estimated_cost) as total_cost')
            ->groupBy('provider')
            ->get()
            ->map(fn ($item): array => [
                'provider' => $item->provider,
                'total_cost' => round((float) $item->total_cost, 4),
            ]);

        $novelsCost = Novel::withCount('chapters')
            ->get()
            ->map(fn ($n): array => [
                'id' => $n->id,
                'title' => $n->title,
                'max_cost_per_episode' => (float) $n->max_cost_per_episode,
                'chapters_count' => $n->chapters_count,
            ]);

        $paginatedUsages = AiUsage::latest()->paginate(15);

        $recentUsagesData = collect($paginatedUsages->items())->map(fn ($u): array => [
            'id' => $u->id,
            'provider' => $u->provider,
            'service' => $u->service,
            'model' => $u->model,
            'estimated_cost' => (float) $u->estimated_cost,
            'created_at' => $u->created_at->diffForHumans(),
        ]);

        return [
            'totalSpend' => round($totalSpend, 4),
            'spendByService' => $spendByService,
            'spendByProvider' => $spendByProvider,
            'novelsCost' => $novelsCost,
            'recentUsages' => [
                'data' => $recentUsagesData,
                'links' => $paginatedUsages->linkCollection()->toArray(),
                'total' => $paginatedUsages->total(),
            ],
        ];
    }

    public function asController(): Response
    {
        return Inertia::render('Costs/Index', $this->handle());
    }
}
