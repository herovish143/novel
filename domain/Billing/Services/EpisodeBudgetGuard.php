<?php

namespace Domain\Billing\Services;

use Domain\Billing\Models\AiUsage;
use Domain\Novel\Models\Chapter;
use Domain\Production\Models\ProductionRun;

class EpisodeBudgetGuard
{
    /**
     * Check current accumulated cost against episode budget limit.
     *
     * @return array{allowed: bool, currentCost: float, limit: float, remaining: float}
     */
    public function check(Chapter $chapter, float $estimatedStepCost = 0.0): array
    {
        $novel = $chapter->novel;
        $limit = (float) ($novel->max_cost_per_episode ?: 5.00);

        $productionRun = ProductionRun::where('chapter_id', $chapter->id)->latest()->first();

        $currentCost = 0.0;
        if ($productionRun) {
            $currentCost = (float) AiUsage::where('production_run_id', $productionRun->id)->sum('actual_cost');
        }

        $totalProjectedCost = $currentCost + $estimatedStepCost;
        $remaining = max(0.0, $limit - $totalProjectedCost);
        $allowed = $totalProjectedCost <= $limit;

        return [
            'allowed' => $allowed,
            'currentCost' => round($currentCost, 4),
            'limit' => round($limit, 2),
            'remaining' => round($remaining, 4),
        ];
    }
}
