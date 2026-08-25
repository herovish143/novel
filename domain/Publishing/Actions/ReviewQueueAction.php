<?php

namespace Domain\Publishing\Actions;

use Domain\Script\Models\Script;
use Domain\Video\Models\VideoProject;
use Domain\Visual\Models\SceneAsset;
use Domain\Voice\Models\AudioSegment;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ReviewQueueAction
{
    use AsAction;

    public function handle(): array
    {
        $pendingScripts = Script::with('chapter.novel')
            ->where('status', 'NEEDS_REVIEW')
            ->orWhere('status', 'GENERATED')
            ->latest()
            ->get()
            ->map(fn ($s): array => [
                'id' => $s->id,
                'chapter_id' => $s->chapter_id,
                'novel_title' => $s->chapter->novel->title ?? 'Unknown Novel',
                'chapter_title' => "Ch. {$s->chapter->chapter_number}: {$s->chapter->title}",
                'version' => $s->version,
                'word_count' => $s->word_count,
                'status' => $s->status,
                'created_at' => $s->created_at->diffForHumans(),
            ]);

        $pendingAudio = AudioSegment::with('scriptSegment.script.chapter.novel')
            ->where('status', 'GENERATED')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($a): array => [
                'id' => $a->id,
                'provider' => $a->provider,
                'duration_ms' => $a->duration_ms,
                'cost' => $a->cost,
                'status' => $a->status,
                'created_at' => $a->created_at->diffForHumans(),
            ]);

        $pendingVisuals = SceneAsset::with('scene.chapter.novel')
            ->where('status', 'GENERATED')
            ->latest()
            ->take(12)
            ->get()
            ->map(fn ($v): array => [
                'id' => $v->id,
                'scene_id' => $v->scene_id,
                'asset_type' => $v->asset_type,
                'storage_path' => $v->storage_path,
                'cost' => $v->cost,
                'status' => $v->status,
                'created_at' => $v->created_at->diffForHumans(),
            ]);

        $pendingVideos = VideoProject::with('chapter.novel')
            ->where('status', 'RENDERED')
            ->orWhere('status', 'GENERATED')
            ->latest()
            ->get()
            ->map(fn ($vp): array => [
                'id' => $vp->id,
                'chapter_id' => $vp->chapter_id,
                'novel_title' => $vp->chapter->novel->title ?? 'Unknown Novel',
                'chapter_title' => "Ch. {$vp->chapter->chapter_number}: {$vp->chapter->title}",
                'duration_ms' => $vp->duration_ms,
                'status' => $vp->status,
                'created_at' => $vp->created_at->diffForHumans(),
            ]);

        return [
            'scripts' => $pendingScripts,
            'audio' => $pendingAudio,
            'visuals' => $pendingVisuals,
            'videos' => $pendingVideos,
        ];
    }

    public function asController(): Response
    {
        return Inertia::render('Reviews/Index', $this->handle());
    }
}
