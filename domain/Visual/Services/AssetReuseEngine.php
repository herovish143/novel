<?php

namespace Domain\Visual\Services;

use Domain\Novel\Models\Chapter;
use Domain\Visual\Models\SceneAsset;

class AssetReuseEngine
{
    /**
     * Check existing visual assets across the novel to find reuse candidates.
     *
     * @param  array<string, mixed>  $sceneRequirements
     * @return array{reusable: bool, confidence: int, asset: SceneAsset|null}
     */
    public function findReuseCandidate(Chapter $chapter, array $sceneRequirements): array
    {
        $novel = $chapter->novel;
        $description = strtolower($sceneRequirements['description'] ?? '');

        if ($description === '') {
            return ['reusable' => false, 'confidence' => 0, 'asset' => null];
        }

        // Search previous scene assets in the same novel
        $candidate = SceneAsset::whereHas('scene.chapter', function ($q) use ($novel): void {
            $q->where('novel_id', $novel->id);
        })
            ->where('status', 'READY')
            ->latest()
            ->first();

        if (! $candidate) {
            return ['reusable' => false, 'confidence' => 0, 'asset' => null];
        }

        // Simple fuzzy match simulation based on prompt matching
        $confidence = 50;
        if (str_contains(strtolower($candidate->prompt), substr($description, 0, 15))) {
            $confidence = 90;
        }

        if ($confidence >= 85) {
            return ['reusable' => true, 'confidence' => $confidence, 'asset' => $candidate];
        }

        return ['reusable' => false, 'confidence' => $confidence, 'asset' => null];
    }
}
