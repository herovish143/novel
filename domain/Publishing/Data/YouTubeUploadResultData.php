<?php

declare(strict_types=1);

namespace Domain\Publishing\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class YouTubeUploadResultData extends Data
{
    public function __construct(
        public string $youtubeVideoId,
        public string $videoUrl,
        public string $status = 'UPLOADED',
    ) {}
}
