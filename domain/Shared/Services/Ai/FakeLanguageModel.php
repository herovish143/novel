<?php

declare(strict_types=1);

namespace Domain\Shared\Services\Ai;

class FakeLanguageModel implements LanguageModel
{
    /**
     * @param  array<string, mixed>  $stubbedStructured
     */
    public function __construct(
        public string $stubbedContent = '',
        public array $stubbedStructured = []
    ) {}

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        string $model = 'gpt-4o',
        float $temperature = 0.70
    ): AiResponse {
        return new AiResponse(
            content: $this->stubbedContent,
            structuredContent: $this->stubbedStructured,
            inputTokens: 150,
            outputTokens: 300,
            model: $model,
            estimatedCost: 0.003
        );
    }
}
