<?php

declare(strict_types=1);

namespace Domain\Visual\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
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
