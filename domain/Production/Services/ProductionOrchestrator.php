<?php

namespace Domain\Production\Services;

use Domain\Billing\Services\EpisodeBudgetGuard;
use Domain\Novel\Actions\AnalyzeChapterAction;
use Domain\Novel\Models\Chapter;
use Domain\Production\Models\ProductionRun;
use Domain\Production\Models\ProductionStep;
use Domain\Script\Actions\GenerateHindiScriptAction;
use Domain\StoryMemory\Actions\UpdateStoryMemoryAction;
use Domain\Video\Actions\RenderVideoAction;
use Domain\Video\Services\FfmpegRenderer;
use Domain\Video\Services\TimelineBuilder;
use Domain\Visual\Actions\PlanScenesAction;
use Domain\Visual\Services\AssetReuseEngine;
use Domain\Visual\Services\ImageGenerator;
use Domain\Visual\Services\ScenePlanner;
use Domain\Voice\Actions\GenerateNarrationAction;
use Domain\Voice\Services\PronunciationProcessor;
use Domain\Voice\Services\SpeechGenerator;
use Domain\Voice\Services\SubtitleGenerator;
use Illuminate\Support\Carbon;
use RuntimeException;

class ProductionOrchestrator
{
    public function __construct(
        protected EpisodeBudgetGuard $budgetGuard,
        protected AnalyzeChapterAction $analyzeAction,
        protected UpdateStoryMemoryAction $memoryAction,
        protected GenerateHindiScriptAction $scriptAction,
        protected GenerateNarrationAction $narrationAction,
        protected PlanScenesAction $scenesAction,
        protected RenderVideoAction $renderAction,
    ) {}

    /**
     * Start or resume production for a chapter.
     */
    public function run(
        Chapter $chapter,
        ?SpeechGenerator $speechGenerator = null,
        ?PronunciationProcessor $pronunciationProcessor = null,
        ?SubtitleGenerator $subtitleGenerator = null,
        ?ScenePlanner $scenePlanner = null,
        ?ImageGenerator $imageGenerator = null,
        ?AssetReuseEngine $reuseEngine = null,
        ?TimelineBuilder $timelineBuilder = null,
        ?FfmpegRenderer $renderer = null
    ): ProductionRun {
        $budget = $this->budgetGuard->check($chapter);
        if (! $budget['allowed']) {
            throw new RuntimeException("Production paused: Projected episode cost exceeds max budget of ₹{$budget['limit']}.");
        }

        $productionRun = ProductionRun::firstOrCreate(
            ['chapter_id' => $chapter->id],
            [
                'status' => 'RUNNING',
                'current_stage' => 'ANALYZE_CHAPTER',
                'started_at' => \Illuminate\Support\Facades\Date::now(),
                'estimated_cost' => 0.50,
                'actual_cost' => 0.00,
            ]
        );

        $productionRun->update(['status' => 'RUNNING']);

        // Step 1: Analyze Chapter & Extract Facts
        $step1 = $this->recordStep($productionRun, 'ANALYZE_CHAPTER');
        if ($step1->status !== 'COMPLETE') {
            $extractedFacts = $this->analyzeAction->handle($chapter);
            $this->memoryAction->handle($chapter, $extractedFacts);
            $step1->update(['status' => 'COMPLETE', 'completed_at' => \Illuminate\Support\Facades\Date::now()]);
        }

        // Step 2: Generate Hindi Script
        $step2 = $this->recordStep($productionRun, 'GENERATE_SCRIPT');
        if ($step2->status !== 'COMPLETE') {
            $script = $this->scriptAction->handle($chapter);
            $step2->update(['status' => 'COMPLETE', 'completed_at' => \Illuminate\Support\Facades\Date::now()]);
        }

        // Approval Gate Check: Pause if script is not approved yet
        $latestScript = $chapter->latestScript;
        if (! $latestScript || $latestScript->status !== 'APPROVED') {
            $productionRun->update([
                'status' => 'WAITING_FOR_APPROVAL',
                'current_stage' => 'SCRIPT_APPROVAL',
            ]);

            return $productionRun;
        }

        // Step 3: Voice Narration & Subtitles (if voice services provided)
        if ($speechGenerator && $pronunciationProcessor && $subtitleGenerator) {
            $step3 = $this->recordStep($productionRun, 'GENERATE_AUDIO');
            if ($step3->status !== 'COMPLETE') {
                $this->narrationAction->handle($latestScript, $speechGenerator, $pronunciationProcessor, $subtitleGenerator);
                $step3->update(['status' => 'COMPLETE', 'completed_at' => \Illuminate\Support\Facades\Date::now()]);
            }
        }

        // Step 4: Scene Planning & Image Generation (if visual services provided)
        if ($scenePlanner && $imageGenerator && $reuseEngine) {
            $step4 = $this->recordStep($productionRun, 'GENERATE_SCENES');
            if ($step4->status !== 'COMPLETE') {
                $this->scenesAction->handle($chapter, $scenePlanner, $imageGenerator, $reuseEngine);
                $step4->update(['status' => 'COMPLETE', 'completed_at' => \Illuminate\Support\Facades\Date::now()]);
            }
        }

        // Step 5: Video Rendering (if render services provided)
        if ($timelineBuilder && $renderer) {
            $step5 = $this->recordStep($productionRun, 'RENDER_VIDEO');
            if ($step5->status !== 'COMPLETE') {
                $this->renderAction->handle($chapter, $timelineBuilder, $renderer);
                $step5->update(['status' => 'COMPLETE', 'completed_at' => \Illuminate\Support\Facades\Date::now()]);
            }
        }

        $productionRun->update([
            'status' => 'COMPLETED',
            'current_stage' => 'FINAL_REVIEW',
            'completed_at' => \Illuminate\Support\Facades\Date::now(),
        ]);

        return $productionRun;
    }

    protected function recordStep(ProductionRun $run, string $stage): ProductionStep
    {
        return ProductionStep::firstOrCreate(
            ['production_run_id' => $run->id, 'stage' => $stage],
            [
                'status' => 'IN_PROGRESS',
                'attempts' => 1,
                'started_at' => \Illuminate\Support\Facades\Date::now(),
            ]
        );
    }
}
