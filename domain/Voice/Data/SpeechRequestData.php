<?php

declare(strict_types=1);

namespace Domain\Voice\Data;

use Spatie\LaravelData\Data;

class SpeechRequestData extends Data
{
    public function __construct(
        public string $text,
        public ?string $voiceId = null,
        public string $model = 'eleven_multilingual_v2',
        public string $language = 'hi',
    ) {}
}
