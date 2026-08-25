<?php

namespace Domain\Novel\Actions;

use Domain\Billing\Services\EpisodeBudgetGuard;
use Domain\Novel\Data\ChapterData;
use Domain\Novel\Data\NovelData;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\ChapterSourceVersion;
use Domain\Production\Models\ProductionRun;
use Domain\Publishing\Models\YouTubePublication;
use Domain\Script\Data\ScriptData;
use Domain\Shared\Models\MediaAsset;
use Domain\StoryMemory\Data\ChapterSummaryData;
use Domain\StoryMemory\Data\StoryEventData;
use Domain\StoryMemory\Models\ChapterFact;
use Domain\Voice\Models\SubtitleFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowChapterAction
{
    use AsAction;

    public function handle(Chapter $chapter, EpisodeBudgetGuard $budgetGuard): array
    {
        $chapter->load(['novel', 'summary', 'events', 'latestScript.segments', 'productionRuns.steps']);

        $narrationUrl = null;
        $masterAudioPath = "novels/{$chapter->novel->slug}/chapters/{$chapter->chapter_number}/audio/narration.mp3";
        if (Storage::disk('public')->exists($masterAudioPath)) {
            $narrationUrl = Storage::disk('public')->url($masterAudioPath);
        }

        $videoUrl = null;
        $finalVideoPath = "novels/{$chapter->novel->slug}/chapters/{$chapter->chapter_number}/video/final.mp4";
        if (Storage::disk('public')->exists($finalVideoPath)) {
            $videoUrl = Storage::disk('public')->url($finalVideoPath);
        }

        $subtitles = SubtitleFile::where('chapter_id', $chapter->id)->get();
        $productionRun = ProductionRun::where('chapter_id', $chapter->id)->latest()->first();
        $publication = YouTubePublication::where('chapter_id', $chapter->id)->latest()->first();
        $budget = $budgetGuard->check($chapter);

        $sourceVersions = ChapterSourceVersion::where('chapter_id', $chapter->id)->orderByDesc('version')->get();
        $facts = ChapterFact::where('chapter_id', $chapter->id)->get();
        $mediaAssets = MediaAsset::where('chapter_id', $chapter->id)->latest()->get();

        return [
            'chapter' => ChapterData::from($chapter),
            'novel' => NovelData::from($chapter->novel),
            'summary' => $chapter->summary ? ChapterSummaryData::from($chapter->summary) : null,
            'events' => StoryEventData::collect($chapter->events),
            'script' => $chapter->latestScript ? ScriptData::from($chapter->latestScript) : null,
            'narrationUrl' => $narrationUrl,
            'videoUrl' => $videoUrl,
            'subtitles' => $subtitles->map(fn ($sub): array => [
                'format' => $sub->format,
                'url' => Storage::disk('public')->url($sub->storage_path),
            ]),
            'productionRun' => $productionRun ? [
                'id' => $productionRun->id,
                'status' => $productionRun->status,
                'current_stage' => $productionRun->current_stage,
                'steps' => $productionRun->steps->map(fn ($s): array => [
                    'stage' => $s->stage,
                    'status' => $s->status,
                ]),
            ] : null,
            'publication' => $publication ? [
                'title' => $publication->title,
                'description' => $publication->description,
                'tags' => $publication->tags,
                'visibility' => $publication->visibility,
                'youtube_video_id' => $publication->youtube_video_id,
                'publish_status' => $publication->publish_status,
                'thumbnail_url' => $publication->thumbnail_path && Storage::disk('public')->exists($publication->thumbnail_path)
                    ? Storage::disk('public')->url($publication->thumbnail_path)
                    : null,
            ] : null,
            'budget' => $budget,
            'sourceVersions' => $sourceVersions,
            'facts' => $facts,
            'mediaAssets' => $mediaAssets,
        ];
    }

    public function asController(Chapter $chapter, EpisodeBudgetGuard $budgetGuard): Response
    {
        $data = $this->handle($chapter, $budgetGuard);

        return Inertia::render('Chapters/Show', $data);
    }
}
