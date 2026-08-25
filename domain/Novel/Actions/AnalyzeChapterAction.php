<?php

namespace Domain\Novel\Actions;

use Domain\Billing\Models\AiUsage;
use Domain\Novel\Models\Chapter;
use Domain\Production\Models\ProductionRun;
use Domain\Shared\Services\Ai\LanguageModel;
use Domain\StoryMemory\Actions\UpdateStoryMemoryAction;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class AnalyzeChapterAction
{
    use AsAction;

    public function __construct(
        protected LanguageModel $ai
    ) {}

    /**
     * Extract structured story facts from raw chapter text using AI.
     *
     * @return array<string, mixed>
     */
    public function handle(Chapter $chapter): array
    {
        $systemPrompt = <<<'SYS'
You are an expert web-novel analyzer. Your task is to extract objective factual information from the chapter text provided. Do not invent details not present in the chapter.
SYS;

        $userPrompt = <<<USER
Analyze Chapter {$chapter->chapter_number}: "{$chapter->title}" of novel "{$chapter->novel->title}".

Chapter Content:
{$chapter->source_text}
USER;

        $schema = [
            'type' => 'object',
            'properties' => [
                'summary' => ['type' => 'string'],
                'characters' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'canonical_name' => ['type' => 'string'],
                            'gender' => ['type' => 'string'],
                            'age_description' => ['type' => 'string'],
                            'physical_description' => ['type' => 'string'],
                            'personality' => ['type' => 'string'],
                            'visual_description' => ['type' => 'string'],
                            'importance' => ['type' => 'string', 'enum' => ['MAIN', 'SECONDARY', 'MINOR']],
                        ],
                        'required' => ['name', 'canonical_name', 'importance'],
                        'additionalProperties' => false,
                    ],
                ],
                'new_characters' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'locations' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'visual_description' => ['type' => 'string'],
                        ],
                        'required' => ['name'],
                        'additionalProperties' => false,
                    ],
                ],
                'events' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'sequence' => ['type' => 'integer'],
                            'event_type' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'importance_score' => ['type' => 'integer'],
                        ],
                        'required' => ['sequence', 'description', 'importance_score'],
                        'additionalProperties' => false,
                    ],
                ],
                'abilities' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'character_name' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['name'],
                        'additionalProperties' => false,
                    ],
                ],
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'owner_character_name' => ['type' => 'string'],
                        ],
                        'required' => ['name'],
                        'additionalProperties' => false,
                    ],
                ],
                'relationships_changed' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'character_name' => ['type' => 'string'],
                            'related_character_name' => ['type' => 'string'],
                            'relationship_type' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['character_name', 'related_character_name', 'relationship_type'],
                        'additionalProperties' => false,
                    ],
                ],
                'important_reveals' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'unresolved_questions' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
            'required' => ['summary', 'characters', 'locations', 'events', 'important_reveals', 'unresolved_questions'],
            'additionalProperties' => false,
        ];

        $aiResponse = $this->ai->generate(
            systemPrompt: $systemPrompt,
            userPrompt: $userPrompt,
            jsonSchema: $schema,
            model: 'gpt-4o'
        );

        $productionRun = ProductionRun::where('chapter_id', $chapter->id)->latest()->first();

        AiUsage::create([
            'production_run_id' => $productionRun?->id,
            'provider' => 'OpenAI',
            'service' => 'CHAPTER_ANALYSIS',
            'model' => $aiResponse->model,
            'input_tokens' => $aiResponse->inputTokens,
            'output_tokens' => $aiResponse->outputTokens,
            'estimated_cost' => $aiResponse->estimatedCost,
            'actual_cost' => $aiResponse->estimatedCost,
        ]);

        $chapter->update([
            'status' => 'ANALYZED',
            'analyzed_at' => now(),
        ]);

        return $aiResponse->structuredContent;
    }

    public function asController(Chapter $chapter, UpdateStoryMemoryAction $memoryAction): RedirectResponse
    {
        $chapter->load('novel');
        $facts = $this->handle($chapter);
        $memoryAction->handle($chapter, $facts);

        return to_route('chapters.show', $chapter->id)->with('success', 'Chapter analyzed and story memory updated successfully.');
    }
}
