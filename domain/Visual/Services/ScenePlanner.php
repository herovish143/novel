<?php

namespace Domain\Visual\Services;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;
use Domain\Shared\Services\Ai\LanguageModel;
use Domain\Visual\Models\Scene;

class ScenePlanner
{
    public function __construct(
        protected LanguageModel $ai,
        protected ScenePromptBuilder $promptBuilder,
        protected AssetReuseEngine $reuseEngine
    ) {}

    /**
     * Plan scenes based on approved script narration and timestamps.
     *
     * @return list<Scene>
     */
    public function plan(Chapter $chapter, Script $script): array
    {
        $novel = $chapter->novel;
        $segments = $script->segments()->orderBy('sequence')->get();

        $scenes = [];
        $sequence = 1;
        $currentMs = 0;

        foreach ($segments as $segment) {
            $durationMs = (int) max(10000, round(($segment->estimated_duration ?: 15) * 1000));
            $endMs = $currentMs + $durationMs;

            $description = "Visual representation for section [{$segment->type}]: {$segment->text}";
            $prompt = $this->promptBuilder->buildPrompt($novel, [
                'description' => $description,
                'location' => 'Dark fantasy landscape',
            ]);

            // Determine camera motion effect
            $motions = ['slow_zoom', 'pan_left', 'pan_right', 'static'];
            $cameraMotion = $motions[$sequence % count($motions)];

            $scene = Scene::create([
                'chapter_id' => $chapter->id,
                'script_id' => $script->id,
                'sequence' => $sequence++,
                'start_ms' => $currentMs,
                'end_ms' => $endMs,
                'scene_type' => 'IMAGE',
                'description' => $description,
                'image_prompt' => $prompt,
                'camera_motion' => $cameraMotion,
                'importance' => 8,
                'status' => 'PLANNED',
            ]);

            $scenes[] = $scene;
            $currentMs = $endMs;
        }

        return $scenes;
    }
}
