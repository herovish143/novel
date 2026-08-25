<?php

namespace Domain\Voice\Services;

use Domain\Voice\Data\SpeechRequestData;
use Domain\Voice\Data\SpeechResultData;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ElevenLabsSpeechGenerator implements SpeechGenerator
{
    public function __construct(
        protected ?string $apiKey = null,
        protected string $baseUrl = 'https://api.elevenlabs.io/v1'
    ) {
        $this->apiKey = $apiKey ?? config('services.elevenlabs.api_key', env('ELEVENLABS_API_KEY'));
    }

    public function generate(SpeechRequestData $request): SpeechResultData
    {
        if (! $this->apiKey) {
            throw new RuntimeException('ElevenLabs API key is missing. Set ELEVENLABS_API_KEY in your environment.');
        }

        $voiceId = $request->voiceId ?? config('services.elevenlabs.default_voice_id', env('ELEVENLABS_VOICE_ID', '21m00Tcm4TlvDq8ikWAM'));

        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(120)
            ->post("{$this->baseUrl}/text-to-speech/{$voiceId}", [
                'text' => $request->text,
                'model_id' => $request->model,
                'voice_settings' => [
                    'stability' => 0.50,
                    'similarity_boost' => 0.75,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('ElevenLabs API request failed: '.$response->body());
        }

        $audioBinary = $response->body();
        $charCount = mb_strlen($request->text);
        // Estimate duration based on char count (~15 chars per sec in Hindi)
        $durationMs = (int) round(($charCount / 15) * 1000);
        // ElevenLabs estimated pricing (~$0.30 per 1,000 chars on creator plan)
        $cost = round(($charCount / 1000) * 0.30, 4);

        return new SpeechResultData(
            audioBinary: $audioBinary,
            durationMs: $durationMs,
            characterCount: $charCount,
            cost: $cost,
            provider: 'ElevenLabs',
            model: $request->model
        );
    }
}
