<?php

declare(strict_types=1);

namespace Domain\Visual\Data;

use Spatie\LaravelData\Data;

class ImageResultData extends Data
{
    public function __construct(
        public string $imageBinary,
        public int $width,
        public int $height,
        public float $cost,
        public string $provider = 'OpenAI',
        public string $model = 'dall-e-3',
    ) {}
}
