<?php

namespace Domain\Script\Actions;

use Domain\Billing\Models\AiUsage;
use Domain\Novel\Models\Chapter;
use Domain\Production\Models\ProductionRun;
use Domain\Script\Models\Script;
use Domain\Script\Models\ScriptSegment;
use Domain\Shared\Services\Ai\LanguageModel;
use Domain\StoryMemory\Services\StoryContextBuilder;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateHindiScriptAction
{
    use AsAction;

    public function __construct(
        protected LanguageModel $ai,
        protected StoryContextBuilder $contextBuilder
    ) {}

    public function handle(Chapter $chapter): Script
    {
        $context = $this->contextBuilder->buildContext($chapter);

        $systemPrompt = <<<'SYS'
You are an expert Hindi web-novel storyteller and explainer.
Your goal is to create a compelling, natural conversational Hindi recap/explanation of the chapter for a video audience.

RULES:
1. Do NOT translate sentence-by-sentence. Write an original recap, explanation, and commentary in conversational Hindi.
2. Preserve original character names and established terms (e.g. Sunny, Nephis, Soul Core, Awakening).
3. Clearly distinguish commentary/analysis from story facts.
4. Keep the narrative engaging with dramatic hooks.
SYS;

        $userPrompt = <<<USER
{$context}

=== CURRENT CHAPTER TO EXPLAIN ===
Chapter {$chapter->chapter_number}: {$chapter->title}

Content:
{$chapter->source_text}

Generate the Hindi script in JSON with these 5 sections:
- hook
- previous_recap
- main_narration
- analysis
- ending_hook
USER;

        $schema = [
            'type' => 'object',
            'properties' => [
                'hook' => ['type' => 'string'],
                'previous_recap' => ['type' => 'string'],
                'main_narration' => ['type' => 'string'],
                'analysis' => ['type' => 'string'],
                'ending_hook' => ['type' => 'string'],
            ],
            'required' => ['hook', 'previous_recap', 'main_narration', 'analysis', 'ending_hook'],
            'additionalProperties' => false,
        ];

        $aiResponse = $this->ai->generate(
            systemPrompt: $systemPrompt,
            userPrompt: $userPrompt,
            jsonSchema: $schema,
            model: 'gpt-4o'
        );

        $structured = $aiResponse->structuredContent;
        $hook = $structured['hook'] ?? '';
        $recap = $structured['previous_recap'] ?? '';
        $narration = $structured['main_narration'] ?? '';
        $analysis = $structured['analysis'] ?? '';
        $ending = $structured['ending_hook'] ?? '';

        $fullScript = implode("\n\n", array_filter([$hook, $recap, $narration, $analysis, $ending]));
        $wordCount = str_word_count($fullScript);
        $characterCount = mb_strlen($fullScript);

        $latestVersion = Script::where('chapter_id', $chapter->id)->max('version') ?? 0;
        $newVersion = $latestVersion + 1;

        $script = Script::create([
            'chapter_id' => $chapter->id,
            'version' => $newVersion,
            'language' => 'hi',
            'status' => 'NEEDS_REVIEW',
            'hook' => $hook,
            'previous_recap' => $recap,
            'main_narration' => $narration,
            'analysis' => $analysis,
            'ending_hook' => $ending,
            'full_script' => $fullScript,
            'word_count' => $wordCount,
            'character_count' => $characterCount,
            'ai_model' => $aiResponse->model,
            'prompt_version' => 'v1',
        ]);

        $this->createSegments($script, [
            'HOOK' => $hook,
            'RECAP' => $recap,
            'STORY' => $narration,
            'COMMENTARY' => $analysis,
            'ENDING' => $ending,
        ]);

        $productionRun = ProductionRun::where('chapter_id', $chapter->id)->latest()->first();

        AiUsage::create([
            'production_run_id' => $productionRun?->id,
            'provider' => 'OpenAI',
            'service' => 'SCRIPT_GENERATION',
            'model' => $aiResponse->model,
            'input_tokens' => $aiResponse->inputTokens,
            'output_tokens' => $aiResponse->outputTokens,
            'characters' => $characterCount,
            'estimated_cost' => $aiResponse->estimatedCost,
            'actual_cost' => $aiResponse->estimatedCost,
        ]);

        $chapter->update([
            'status' => 'SCRIPT_REVIEW',
            'scripted_at' => now(),
        ]);

        return $script;
    }

    public function asController(Chapter $chapter): RedirectResponse
    {
        $chapter->load('novel');
        $script = $this->handle($chapter);

        return to_route('scripts.review', $script->id)->with('success', 'Hindi script generated successfully.');
    }

    protected function createSegments(Script $script, array $sections): void
    {
        $seq = 1;
        foreach ($sections as $type => $text) {
            if (trim($text) === '') {
                continue;
            }

            $chunks = str_split($text, 1200);
            foreach ($chunks as $chunk) {
                ScriptSegment::create([
                    'script_id' => $script->id,
                    'sequence' => $seq++,
                    'type' => $type,
                    'text' => trim($chunk),
                    'estimated_duration' => round(mb_strlen($chunk) / 15, 2),
                    'status' => 'PENDING',
                ]);
            }
        }
    }
}
