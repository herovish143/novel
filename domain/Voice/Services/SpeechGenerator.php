<?php

declare(strict_types=1);

namespace Domain\Voice\Services;

use Domain\Voice\Data\SpeechRequestData;
use Domain\Voice\Data\SpeechResultData;

interface SpeechGenerator
{
    public function generate(SpeechRequestData $request): SpeechResultData;
}
