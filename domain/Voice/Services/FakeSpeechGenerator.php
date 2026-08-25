<?php

namespace Domain\Voice\Services;

use Domain\Voice\Data\SpeechRequestData;
use Domain\Voice\Data\SpeechResultData;

class FakeSpeechGenerator implements SpeechGenerator
{
    public function generate(SpeechRequestData $request): SpeechResultData
    {
        // Sample 1-second dummy silent MP3 header / audio bytes for testing
        $dummyMp3 = base64_decode('SUQzBAAAAAAAI1RQRTEAAAAZAAAARmFrZSBFbGV2ZW5MYWJzIEF1ZGlvAABNUEQ=');
        $charCount = mb_strlen($request->text);
        $durationMs = max(1000, (int) round(($charCount / 15) * 1000));
        $cost = round(($charCount / 1000) * 0.30, 4);

        return new SpeechResultData(
            audioBinary: $dummyMp3 ?: 'DUMMY_AUDIO_DATA',
            durationMs: $durationMs,
            characterCount: $charCount,
            cost: $cost,
            provider: 'FakeElevenLabs',
            model: 'eleven_multilingual_v2'
        );
    }
}
