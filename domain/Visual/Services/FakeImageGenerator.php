<?php

declare(strict_types=1);

namespace Domain\Visual\Services;

use Domain\Visual\Data\ImageRequestData;
use Domain\Visual\Data\ImageResultData;

class FakeImageGenerator implements ImageGenerator
{
    public function generate(ImageRequestData $request): ImageResultData
    {
        // Generate dummy 1792x1024 SVG image binary for testing
        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="1792" height="1024" viewBox="0 0 1792 1024">
  <rect width="1792" height="1024" fill="#1e1e2e"/>
  <text x="896" y="512" font-family="sans-serif" font-size="48" fill="#cba6f7" text-anchor="middle">
    Fake DALL-E 3 Scene Visual
  </text>
</svg>
SVG;

        [$w, $h] = explode('x', $request->size);

        return new ImageResultData(
            imageBinary: $svg,
            width: (int) $w,
            height: (int) $h,
            cost: 0.040,
            provider: 'FakeOpenAI',
            model: 'dall-e-3'
        );
    }
}
