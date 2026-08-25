<?php

namespace Domain\Voice\Jobs;

use Domain\Script\Models\Script;
use Domain\Voice\Models\AudioSegment;
use Domain\Voice\Models\SubtitleFile;
use Domain\Voice\Services\SubtitleGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MergeAudioSegmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Script $script
    ) {}

    public function handle(SubtitleGenerator $subtitleGenerator): void
    {
        $chapter = $this->script->chapter;
        $novel = $chapter->novel;

        $segments = $this->script->segments()
            ->orderBy('sequence')
            ->get();

        $audioSegments = AudioSegment::whereIn('script_segment_id', $segments->pluck('id'))
            ->get()
            ->keyBy('script_segment_id');

        $mergedBinary = '';
        $currentMs = 0;
        $timedSegments = [];

        foreach ($segments as $seg) {
            $audio = $audioSegments->get($seg->id);
            if ($audio && Storage::disk('public')->exists($audio->storage_path)) {
                $content = Storage::disk('public')->get($audio->storage_path);
                $mergedBinary .= $content;

                $durationMs = $audio->duration_ms > 0 ? $audio->duration_ms : (int) ($seg->estimated_duration * 1000);
                $endMs = $currentMs + $durationMs;

                $timedSegments[] = [
                    'text' => $seg->text,
                    'start_ms' => $currentMs,
                    'end_ms' => $endMs,
                ];

                $currentMs = $endMs;
            }
        }

        // Save narration.mp3
        $masterAudioPath = "novels/{$novel->slug}/chapters/{$chapter->chapter_number}/audio/narration.mp3";
        Storage::disk('public')->put($masterAudioPath, $mergedBinary);

        // Generate Subtitles (SRT and ASS)
        $subtitles = $subtitleGenerator->generate($chapter, $this->script, $timedSegments);

        $srtPath = "novels/{$novel->slug}/chapters/{$chapter->chapter_number}/subtitles/episode.srt";
        $assPath = "novels/{$novel->slug}/chapters/{$chapter->chapter_number}/subtitles/episode.ass";

        Storage::disk('public')->put($srtPath, $subtitles['srt']);
        Storage::disk('public')->put($assPath, $subtitles['ass']);

        SubtitleFile::updateOrCreate(
            ['chapter_id' => $chapter->id, 'script_id' => $this->script->id, 'format' => 'SRT'],
            ['storage_path' => $srtPath, 'language' => $novel->output_language]
        );

        SubtitleFile::updateOrCreate(
            ['chapter_id' => $chapter->id, 'script_id' => $this->script->id, 'format' => 'ASS'],
            ['storage_path' => $assPath, 'language' => $novel->output_language]
        );

        $chapter->update(['status' => 'AUDIO_GENERATED']);
    }
}
