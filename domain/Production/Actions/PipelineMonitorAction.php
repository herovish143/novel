<?php

namespace Domain\Production\Actions;

use Domain\Production\Models\ProductionRun;
use Domain\Production\Models\ProductionStep;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class PipelineMonitorAction
{
    use AsAction;

    public function handle(): array
    {
        $paginatedRuns = ProductionRun::with(['chapter.novel', 'steps'])
            ->latest()
            ->paginate(15);

        $runsData = collect($paginatedRuns->items())->map(fn ($r): array => [
            'id' => $r->id,
            'chapter_id' => $r->chapter_id,
            'novel_title' => $r->chapter->novel->title ?? 'Unknown Novel',
            'chapter_title' => "Ch. {$r->chapter->chapter_number}: {$r->chapter->title}",
            'status' => $r->status,
            'current_stage' => $r->current_stage,
            'started_at' => $r->started_at?->diffForHumans() ?? 'Just now',
            'steps' => $r->steps->map(fn ($s): array => [
                'stage' => $s->stage,
                'status' => $s->status,
                'attempts' => $s->attempts,
                'error' => $s->error,
            ]),
        ]);

        $activeRunsCount = ProductionRun::whereIn('status', ['RUNNING', 'IMPORTED'])->count();
        $failedStepsCount = ProductionStep::where('status', 'FAILED')->count();

        $queues = [
            ['name' => 'default', 'depth' => 0, 'status' => 'HEALTHY'],
            ['name' => 'ai', 'depth' => 0, 'status' => 'HEALTHY'],
            ['name' => 'tts', 'depth' => 0, 'status' => 'HEALTHY'],
            ['name' => 'images', 'depth' => 0, 'status' => 'HEALTHY'],
            ['name' => 'render', 'depth' => 0, 'status' => 'HEALTHY'],
            ['name' => 'publish', 'depth' => 0, 'status' => 'HEALTHY'],
        ];

        return [
            'runs' => [
                'data' => $runsData,
                'links' => $paginatedRuns->linkCollection()->toArray(),
                'total' => $paginatedRuns->total(),
            ],
            'activeRunsCount' => $activeRunsCount,
            'failedStepsCount' => $failedStepsCount,
            'queues' => $queues,
        ];
    }

    public function asController(): Response
    {
        return Inertia::render('Pipeline/Index', $this->handle());
    }
}
