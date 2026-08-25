<?php

declare(strict_types=1);

namespace Domain\Publishing\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class YouTubeUploadData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public array $tags,
        public string $videoFilePath,
        public ?string $thumbnailPath = null,
        public string $visibility = 'UNLISTED',
    ) {}
}
