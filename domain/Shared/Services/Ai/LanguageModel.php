<?php

declare(strict_types=1);

namespace Domain\Shared\Services\Ai;

interface LanguageModel
{
    /**
     * Generate text or structured JSON using prompt and optional JSON schema.
     *
     * @param  array<string, mixed>|null  $jsonSchema
     */
    public function generate(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        string $model = 'gpt-4o',
        float $temperature = 0.70
    ): AiResponse;
}
