<?php

namespace Domain\Video\Services;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;
use Domain\Video\Models\VideoProject;
use Domain\Video\Models\VideoTimelineItem;
use Domain\Voice\Models\SubtitleFile;
use Illuminate\Support\Facades\Storage;

class TimelineBuilder
{
    /**
     * Build rendering timeline items & manifest for a chapter's video project.
     *
     * @return array{project: VideoProject, manifest: array<string, mixed>}
     */
    public function build(Chapter $chapter, Script $script): array
    {
        $novel = $chapter->novel;

        $project = VideoProject::create([
            'chapter_id' => $chapter->id,
            'script_id' => $script->id,
            'resolution' => '1920x1080',
            'fps' => 30,
            'status' => 'PENDING',
            'duration_ms' => 0,
            'cost' => 0.15, // Rendering compute cost estimate
        ]);

        $scenes = $chapter->scenes()->orderBy('sequence')->get();
        $totalDurationMs = 0;
        $manifestScenes = [];

        foreach ($scenes as $scene) {
            $asset = $scene->assets->first();
            $imagePath = $asset ? Storage::disk('public')->path($asset->storage_path) : null;
            $durationSec = max(5.0, round(($scene->end_ms - $scene->start_ms) / 1000, 2));

            VideoTimelineItem::create([
                'video_project_id' => $project->id,
                'sequence' => $scene->sequence,
                'type' => 'IMAGE',
                'start_ms' => $scene->start_ms,
                'end_ms' => $scene->end_ms,
                'asset_id' => $asset?->id,
                'transition' => 'crossfade',
                'animation' => $scene->camera_motion ?: 'slow_zoom',
            ]);

            $manifestScenes[] = [
                'sequence' => $scene->sequence,
                'image_path' => $imagePath,
                'duration' => $durationSec,
                'effect' => $scene->camera_motion ?: 'slow_zoom',
            ];

            $totalDurationMs = max($totalDurationMs, $scene->end_ms);
        }

        $audioPath = Storage::disk('public')->path("novels/{$novel->slug}/chapters/{$chapter->chapter_number}/audio/narration.mp3");
        $assSubtitle = SubtitleFile::where('chapter_id', $chapter->id)->where('format', 'ASS')->first();
        $subtitlePath = $assSubtitle ? Storage::disk('public')->path($assSubtitle->storage_path) : null;

        $project->update(['duration_ms' => $totalDurationMs]);

        $manifest = [
            'resolution' => '1920x1080',
            'fps' => 30,
            'audio' => $audioPath,
            'subtitles' => $subtitlePath,
            'scenes' => $manifestScenes,
        ];

        return [
            'project' => $project,
            'manifest' => $manifest,
        ];
    }
}
