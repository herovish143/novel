<?php

namespace Domain\Script\Actions;

use Domain\Billing\Models\AiUsage;
use Domain\Production\Models\ProductionRun;
use Domain\Script\Models\Script;
use Domain\Shared\Services\Ai\LanguageModel;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyHindiScriptAction
{
    use AsAction;

    public function __construct(
        protected LanguageModel $ai
    ) {}

    public function handle(Script $script, array $chapterFacts): array
    {
        $systemPrompt = <<<'SYS'
You are an expert fact-checker for story explanations. Compare the generated Hindi explanation script against the verified facts of the chapter. Identify any factual contradictions, invented plot points, or missing critical events.
SYS;

        $userPrompt = <<<USER
=== VERIFIED CHAPTER FACTS ===
Summary: {$chapterFacts['summary']}
Events:
USER;

        foreach ($chapterFacts['events'] ?? [] as $event) {
            $userPrompt .= "\n- [Seq {$event['sequence']}] {$event['description']}";
        }

        $userPrompt .= <<<USER


=== GENERATED HINDI SCRIPT ===
{$script->full_script}

Verify if the Hindi script accurately reflects the facts without high-severity contradictions.
USER;

        $schema = [
            'type' => 'object',
            'properties' => [
                'valid' => ['type' => 'boolean'],
                'issues' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'section' => ['type' => 'string'],
                            'severity' => ['type' => 'string', 'enum' => ['high', 'medium', 'low']],
                            'problem' => ['type' => 'string'],
                        ],
                        'required' => ['section', 'severity', 'problem'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required' => ['valid', 'issues'],
            'additionalProperties' => false,
        ];

        $aiResponse = $this->ai->generate(
            systemPrompt: $systemPrompt,
            userPrompt: $userPrompt,
            jsonSchema: $schema,
            model: 'gpt-4o'
        );

        $productionRun = ProductionRun::where('chapter_id', $script->chapter_id)->latest()->first();

        AiUsage::create([
            'production_run_id' => $productionRun?->id,
            'provider' => 'OpenAI',
            'service' => 'SCRIPT_VERIFICATION',
            'model' => $aiResponse->model,
            'input_tokens' => $aiResponse->inputTokens,
            'output_tokens' => $aiResponse->outputTokens,
            'estimated_cost' => $aiResponse->estimatedCost,
            'actual_cost' => $aiResponse->estimatedCost,
        ]);

        return $aiResponse->structuredContent;
    }

    public function asController(Script $script): RedirectResponse
    {
        $script->load(['chapter.summary', 'chapter.events']);
        $chapterFacts = [
            'summary' => $script->chapter->summary?->summary ?? '',
            'events' => $script->chapter->events->toArray(),
        ];

        $verification = $this->handle($script, $chapterFacts);

        return back()->with('verification', $verification);
    }
}
