<?php

declare(strict_types=1);

namespace Domain\Visual\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ImageRequestData extends Data
{
    public function __construct(
        public string $prompt,
        public string $size = '1792x1024',
        public string $quality = 'standard',
        public string $model = 'dall-e-3',
    ) {}
}
