<?php

namespace Domain\Visual\Actions;

use Domain\Visual\Jobs\GenerateSceneImageJob;
use Domain\Visual\Models\Scene;
use Domain\Visual\Services\AssetReuseEngine;
use Domain\Visual\Services\ImageGenerator;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class RegenerateSceneAction
{
    use AsAction;

    public function handle(
        Scene $scene,
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine
    ): void {
        $job = new GenerateSceneImageJob($scene, forceRegenerate: true);
        $job->handle($imageGenerator, $reuseEngine);
    }

    public function asController(
        Scene $scene,
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine
    ): RedirectResponse {
        $this->handle($scene, $imageGenerator, $reuseEngine);

        return to_route('scenes.editor', $scene->chapter_id)->with('success', 'Scene image regenerated.');
    }
}
