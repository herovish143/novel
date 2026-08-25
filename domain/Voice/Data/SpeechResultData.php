<?php

declare(strict_types=1);

namespace Domain\Voice\Data;

use Spatie\LaravelData\Data;

class SpeechResultData extends Data
{
    public function __construct(
        public string $audioBinary,
        public int $durationMs,
        public int $characterCount,
        public float $cost,
        public string $provider = 'ElevenLabs',
        public string $model = 'eleven_multilingual_v2',
    ) {}
}
