<?php

namespace Domain\Video\Actions;

use Domain\Novel\Models\Chapter;
use Domain\Video\Jobs\RenderVideoJob;
use Domain\Video\Services\FfmpegRenderer;
use Domain\Video\Services\TimelineBuilder;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class RenderVideoAction
{
    use AsAction;

    public function handle(
        Chapter $chapter,
        TimelineBuilder $timelineBuilder,
        FfmpegRenderer $renderer
    ): void {
        $job = new RenderVideoJob($chapter);
        $job->handle($timelineBuilder, $renderer);
    }

    public function asController(
        Chapter $chapter,
        TimelineBuilder $timelineBuilder,
        FfmpegRenderer $renderer
    ): RedirectResponse {
        $this->handle($chapter, $timelineBuilder, $renderer);

        return to_route('chapters.show', $chapter->id)->with('success', '1080p video rendered successfully.');
    }
}
