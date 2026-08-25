<?php

declare(strict_types=1);

namespace Domain\Publishing\Data;

use Spatie\LaravelData\Data;

class YouTubeUploadResultData extends Data
{
    public function __construct(
        public string $youtubeVideoId,
        public string $videoUrl,
        public string $status = 'UPLOADED',
    ) {}
}
