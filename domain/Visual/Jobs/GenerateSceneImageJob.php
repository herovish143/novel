<?php

namespace Domain\Visual\Jobs;

use Domain\Billing\Models\AiUsage;
use Domain\Production\Models\ProductionRun;
use Domain\Visual\Data\ImageRequestData;
use Domain\Visual\Models\Scene;
use Domain\Visual\Models\SceneAsset;
use Domain\Visual\Services\AssetReuseEngine;
use Domain\Visual\Services\ImageGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateSceneImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Scene $scene,
        public bool $forceRegenerate = false
    ) {}

    public function handle(
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine
    ): void {
        $chapter = $this->scene->chapter;
        $novel = $chapter->novel;

        // Check asset reuse if not forced
        if (! $this->forceRegenerate) {
            $reuse = $reuseEngine->findReuseCandidate($chapter, ['description' => $this->scene->description]);
            if ($reuse['reusable'] && $reuse['asset']) {
                $candidate = $reuse['asset'];

                SceneAsset::create([
                    'scene_id' => $this->scene->id,
                    'asset_type' => 'IMAGE',
                    'provider' => 'ReusedAsset',
                    'prompt' => $this->scene->image_prompt,
                    'storage_path' => $candidate->storage_path,
                    'width' => $candidate->width,
                    'height' => $candidate->height,
                    'cost' => 0.00, // $0 cost for asset reuse!
                    'status' => 'READY',
                    'metadata' => ['reused_from_asset_id' => $candidate->id, 'confidence' => $reuse['confidence']],
                ]);

                $this->scene->update(['status' => 'REUSED']);

                return;
            }
        }

        // Call Image Generator (DALL-E 3)
        $requestData = new ImageRequestData(
            prompt: $this->scene->image_prompt,
            size: '1792x1024'
        );

        $result = $imageGenerator->generate($requestData);

        $storagePath = "novels/{$novel->slug}/chapters/{$chapter->chapter_number}/scenes/scene_{$this->scene->sequence}.webp";
        Storage::disk('public')->put($storagePath, $result->imageBinary);

        SceneAsset::create([
            'scene_id' => $this->scene->id,
            'asset_type' => 'IMAGE',
            'provider' => $result->provider,
            'prompt' => $this->scene->image_prompt,
            'storage_path' => $storagePath,
            'width' => $result->width,
            'height' => $result->height,
            'cost' => $result->cost,
            'status' => 'READY',
        ]);

        $this->scene->update(['status' => 'COMPLETED']);

        $productionRun = ProductionRun::where('chapter_id', $chapter->id)->latest()->first();

        AiUsage::create([
            'production_run_id' => $productionRun?->id,
            'provider' => $result->provider,
            'service' => 'IMAGE_GENERATION',
            'model' => $result->model,
            'images' => 1,
            'estimated_cost' => $result->cost,
            'actual_cost' => $result->cost,
        ]);
    }
}
