<?php

declare(strict_types=1);

namespace Domain\Visual\Services;

use Domain\Visual\Data\ImageRequestData;
use Domain\Visual\Data\ImageResultData;

interface ImageGenerator
{
    public function generate(ImageRequestData $request): ImageResultData;
}
