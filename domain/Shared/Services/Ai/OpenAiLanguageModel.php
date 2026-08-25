<?php

namespace Domain\Shared\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiLanguageModel implements LanguageModel
{
    public function __construct(
        protected ?string $apiKey = null,
        protected string $baseUrl = 'https://api.openai.com/v1'
    ) {
        $this->apiKey = $apiKey ?? config('services.openai.api_key', env('OPENAI_API_KEY'));
    }

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        string $model = 'gpt-4o',
        float $temperature = 0.70
    ): AiResponse {
        if (! $this->apiKey) {
            throw new RuntimeException('OpenAI API key is missing. Set OPENAI_API_KEY in your environment.');
        }

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $temperature,
        ];

        if ($jsonSchema !== null) {
            $payload['response_format'] = [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'response',
                    'strict' => true,
                    'schema' => $jsonSchema,
                ],
            ];
        }

        $response = Http::withToken($this->apiKey)
            ->baseUrl($this->baseUrl)
            ->timeout(60)
            ->post('/chat/completions', $payload);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API request failed: '.$response->body());
        }

        $data = $response->json();
        $rawContent = $data['choices'][0]['message']['content'] ?? '';
        $inputTokens = $data['usage']['prompt_tokens'] ?? 0;
        $outputTokens = $data['usage']['completion_tokens'] ?? 0;

        $structured = [];
        if ($jsonSchema !== null && $rawContent !== '') {
            $structured = json_decode($rawContent, true) ?? [];
        }

        // Estimate cost ($2.50 per 1M input, $10.00 per 1M output for gpt-4o)
        $cost = ($inputTokens * 0.0000025) + ($outputTokens * 0.000010);

        return new AiResponse(
            content: $rawContent,
            structuredContent: $structured,
            inputTokens: $inputTokens,
            outputTokens: $outputTokens,
            model: $model,
            estimatedCost: round($cost, 6)
        );
    }
}
