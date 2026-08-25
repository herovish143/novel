<?php

namespace Domain\Video\Jobs;

use Domain\Billing\Models\AiUsage;
use Domain\Novel\Models\Chapter;
use Domain\Production\Models\ProductionRun;
use Domain\Video\Services\FfmpegRenderer;
use Domain\Video\Services\TimelineBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class RenderVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Chapter $chapter
    ) {}

    public function handle(
        TimelineBuilder $timelineBuilder,
        FfmpegRenderer $renderer
    ): void {
        $script = $this->chapter->latestScript;
        if (! $script) {
            return;
        }

        $buildResult = $timelineBuilder->build($this->chapter, $script);
        $project = $buildResult['project'];
        $manifest = $buildResult['manifest'];

        $project->update([
            'status' => 'RENDERING',
            'render_started_at' => \Illuminate\Support\Facades\Date::now(),
        ]);

        $outputPath = $renderer->render($this->chapter, $project, $manifest);

        $project->update([
            'status' => 'COMPLETED',
            'output_path' => $outputPath,
            'render_completed_at' => \Illuminate\Support\Facades\Date::now(),
        ]);

        $this->chapter->update(['status' => 'VIDEO_RENDERED']);

        $productionRun = ProductionRun::where('chapter_id', $this->chapter->id)->latest()->first();

        AiUsage::create([
            'production_run_id' => $productionRun?->id,
            'provider' => 'FFmpeg',
            'service' => 'RENDERING',
            'model' => 'ffmpeg-1080p',
            'seconds' => (int) round($project->duration_ms / 1000),
            'estimated_cost' => $project->cost,
            'actual_cost' => $project->cost,
        ]);
    }
}
