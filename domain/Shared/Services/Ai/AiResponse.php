<?php

declare(strict_types=1);

namespace Domain\Shared\Services\Ai;

class AiResponse
{
    /**
     * @param  array<string, mixed>  $structuredContent
     */
    public function __construct(
        public string $content,
        public array $structuredContent = [],
        public int $inputTokens = 0,
        public int $outputTokens = 0,
        public string $model = 'gpt-4o',
        public float $estimatedCost = 0.00
    ) {}
}
