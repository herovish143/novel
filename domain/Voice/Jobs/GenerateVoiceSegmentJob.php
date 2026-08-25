<?php

namespace Domain\Voice\Jobs;

use Domain\Billing\Models\AiUsage;
use Domain\Production\Models\ProductionRun;
use Domain\Script\Models\ScriptSegment;
use Domain\Voice\Data\SpeechRequestData;
use Domain\Voice\Models\AudioSegment;
use Domain\Voice\Services\PronunciationProcessor;
use Domain\Voice\Services\SpeechGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateVoiceSegmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ScriptSegment $segment
    ) {}

    public function handle(
        SpeechGenerator $speechGenerator,
        PronunciationProcessor $pronunciationProcessor
    ): void {
        $script = $this->segment->script;
        $chapter = $script->chapter;
        $novel = $chapter->novel;

        // Apply pronunciation overrides
        $text = $pronunciationProcessor->process($novel, $this->segment->text);

        $request = new SpeechRequestData(
            text: $text,
            voiceId: $novel->default_voice_id,
            language: $novel->output_language
        );

        $result = $speechGenerator->generate($request);

        $storagePath = "novels/{$novel->slug}/chapters/{$chapter->chapter_number}/audio/segment_{$this->segment->sequence}.mp3";
        Storage::disk('public')->put($storagePath, $result->audioBinary);

        AudioSegment::updateOrCreate(
            ['script_segment_id' => $this->segment->id],
            [
                'provider' => $result->provider,
                'voice_id' => $request->voiceId,
                'model' => $result->model,
                'storage_path' => $storagePath,
                'duration_ms' => $result->durationMs,
                'character_count' => $result->characterCount,
                'cost' => $result->cost,
                'status' => 'GENERATED',
            ]
        );

        $this->segment->update([
            'estimated_duration' => round($result->durationMs / 1000, 2),
            'status' => 'COMPLETED',
        ]);

        $productionRun = ProductionRun::where('chapter_id', $chapter->id)->latest()->first();

        AiUsage::create([
            'production_run_id' => $productionRun?->id,
            'provider' => $result->provider,
            'service' => 'TTS',
            'model' => $result->model,
            'characters' => $result->characterCount,
            'estimated_cost' => $result->cost,
            'actual_cost' => $result->cost,
        ]);
    }
}
