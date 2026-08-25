<?php

namespace Domain\Visual\Actions;

use Domain\Novel\Models\Chapter;
use Domain\Visual\Jobs\GenerateSceneImageJob;
use Domain\Visual\Services\AssetReuseEngine;
use Domain\Visual\Services\ImageGenerator;
use Domain\Visual\Services\ScenePlanner;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class PlanScenesAction
{
    use AsAction;

    public function handle(
        Chapter $chapter,
        ScenePlanner $scenePlanner,
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine
    ): void {
        $script = $chapter->latestScript;
        if (! $script) {
            return;
        }

        // Delete existing scenes for fresh re-planning
        $chapter->scenes()->delete();

        $scenes = $scenePlanner->plan($chapter, $script);

        foreach ($scenes as $scene) {
            $job = new GenerateSceneImageJob($scene);
            $job->handle($imageGenerator, $reuseEngine);
        }

        $chapter->update(['status' => 'IMAGES_GENERATED']);
    }

    public function asController(
        Chapter $chapter,
        ScenePlanner $scenePlanner,
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine
    ): RedirectResponse {
        $this->handle($chapter, $scenePlanner, $imageGenerator, $reuseEngine);

        return to_route('scenes.editor', $chapter->id)->with('success', 'Visual scenes planned and generated successfully.');
    }
}
