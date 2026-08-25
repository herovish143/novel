<?php

declare(strict_types=1);

namespace Domain\Publishing\Services;

use Domain\Novel\Models\Chapter;
use Domain\Visual\Data\ImageRequestData;
use Domain\Visual\Services\ImageGenerator;
use Illuminate\Support\Facades\Storage;

class ThumbnailPipeline
{
    public function __construct(
        protected ImageGenerator $imageGenerator
    ) {}

    /**
     * Generate custom 16:9 episode thumbnail background.
     */
    public function generate(Chapter $chapter): string
    {
        $novel = $chapter->novel;
        $summary = $chapter->summary?->summary ?: $chapter->title;

        $prompt = "Epic YouTube thumbnail background, high impact, dark fantasy cinematic style, dramatic lighting, intense climax scene for {$novel->title} Chapter {$chapter->chapter_number}: {$summary}. Widescreen 16:9, no text.";

        $requestData = new ImageRequestData(
            prompt: $prompt,
            size: '1792x1024'
        );

        $result = $this->imageGenerator->generate($requestData);

        $storagePath = "novels/{$novel->slug}/chapters/{$chapter->chapter_number}/thumbnails/thumbnail.webp";
        Storage::disk('public')->put($storagePath, $result->imageBinary);

        return $storagePath;
    }
}
