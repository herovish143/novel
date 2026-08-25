<?php

namespace Domain\Production\Actions;

use Domain\Novel\Models\Chapter;
use Domain\Production\Services\ProductionOrchestrator;
use Domain\Video\Services\FfmpegRenderer;
use Domain\Video\Services\TimelineBuilder;
use Domain\Visual\Services\AssetReuseEngine;
use Domain\Visual\Services\ImageGenerator;
use Domain\Visual\Services\ScenePlanner;
use Domain\Voice\Services\PronunciationProcessor;
use Domain\Voice\Services\SpeechGenerator;
use Domain\Voice\Services\SubtitleGenerator;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class StartProductionAction
{
    use AsAction;

    public function handle(
        Chapter $chapter,
        ProductionOrchestrator $orchestrator,
        SpeechGenerator $speechGenerator,
        PronunciationProcessor $pronunciationProcessor,
        SubtitleGenerator $subtitleGenerator,
        ScenePlanner $scenePlanner,
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine,
        TimelineBuilder $timelineBuilder,
        FfmpegRenderer $renderer
    ): void {
        $orchestrator->run(
            $chapter,
            $speechGenerator,
            $pronunciationProcessor,
            $subtitleGenerator,
            $scenePlanner,
            $imageGenerator,
            $reuseEngine,
            $timelineBuilder,
            $renderer
        );
    }

    public function asController(
        Chapter $chapter,
        ProductionOrchestrator $orchestrator,
        SpeechGenerator $speechGenerator,
        PronunciationProcessor $pronunciationProcessor,
        SubtitleGenerator $subtitleGenerator,
        ScenePlanner $scenePlanner,
        ImageGenerator $imageGenerator,
        AssetReuseEngine $reuseEngine,
        TimelineBuilder $timelineBuilder,
        FfmpegRenderer $renderer
    ): RedirectResponse {
        $this->handle(
            $chapter,
            $orchestrator,
            $speechGenerator,
            $pronunciationProcessor,
            $subtitleGenerator,
            $scenePlanner,
            $imageGenerator,
            $reuseEngine,
            $timelineBuilder,
            $renderer
        );

        return to_route('chapters.show', $chapter->id)->with('success', 'Production pipeline launched successfully.');
    }
}
