<?php

declare(strict_types=1);

namespace Domain\Visual\Data;

use Spatie\LaravelData\Data;

class ImageRequestData extends Data
{
    public function __construct(
        public string $prompt,
        public string $size = '1792x1024',
        public string $quality = 'standard',
        public string $model = 'dall-e-3',
    ) {}
}
