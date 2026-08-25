<?php

namespace Domain\Voice\Actions;

use Domain\Script\Models\Script;
use Domain\Voice\Jobs\GenerateVoiceSegmentJob;
use Domain\Voice\Jobs\MergeAudioSegmentsJob;
use Domain\Voice\Services\PronunciationProcessor;
use Domain\Voice\Services\SpeechGenerator;
use Domain\Voice\Services\SubtitleGenerator;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateNarrationAction
{
    use AsAction;

    public function handle(
        Script $script,
        SpeechGenerator $speechGenerator,
        PronunciationProcessor $pronunciationProcessor,
        SubtitleGenerator $subtitleGenerator
    ): void {
        $segments = $script->segments()
            ->orderBy('sequence')
            ->get();

        // Process each segment TTS inline or synchronously if running directly
        foreach ($segments as $segment) {
            $job = new GenerateVoiceSegmentJob($segment);
            $job->handle($speechGenerator, $pronunciationProcessor);
        }

        // Merge audio segments into master narration.mp3 and build SRT/ASS subtitles
        $mergeJob = new MergeAudioSegmentsJob($script);
        $mergeJob->handle($subtitleGenerator);
    }

    public function asController(
        Script $script,
        SpeechGenerator $speechGenerator,
        PronunciationProcessor $pronunciationProcessor,
        SubtitleGenerator $subtitleGenerator
    ): RedirectResponse {
        $this->handle($script, $speechGenerator, $pronunciationProcessor, $subtitleGenerator);

        return to_route('chapters.show', $script->chapter_id)->with('success', 'Voice narration and subtitles generated successfully.');
    }
}
