<?php

declare(strict_types=1);

namespace Domain\Publishing\Services;

use Domain\Novel\Models\Chapter;
use Domain\Shared\Services\Ai\LanguageModel;

class YouTubeMetadataGenerator
{
    public function __construct(
        protected LanguageModel $ai
    ) {}

    /**
     * Generate high-CTR Hindi video metadata (Title, Description, Tags).
     *
     * @return array{title: string, description: string, tags: list<string>}
     */
    public function generate(Chapter $chapter): array
    {
        $novel = $chapter->novel;
        $summary = $chapter->summary?->summary ?: $chapter->title;

        $systemPrompt = 'You are a YouTube SEO expert specializing in Hindi web novel explanations.';
        $userPrompt = <<<PROMPT
Generate YouTube video metadata for a web novel Hindi explanation video:

Novel Title: {$novel->title}
Chapter Number: {$chapter->chapter_number}
Chapter Title: {$chapter->title}
Chapter Facts Summary: {$summary}

Return JSON with keys:
- title: High CTR clicky Hindi title including chapter number (e.g. "रहस्यमयी गुफा का सच! | Chapter {$chapter->chapter_number} Hindi Explanation")
- description: Engaging Hindi description explaining the episode, chapter timestamps, and channel call to action.
- tags: Array of 8-12 relevant YouTube search tags (e.g. ["web novel hindi", "shadow slave chapter {$chapter->chapter_number}", "hindi audiobook recap"])
PROMPT;

        $schema = [
            'name' => 'youtube_metadata',
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'title' => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                    'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
                ],
                'required' => ['title', 'description', 'tags'],
            ],
        ];

        $response = $this->ai->generate($systemPrompt, $userPrompt, $schema);
        $data = $response->structuredContent;

        return [
            'title' => $data['title'] ?? "{$novel->title} - Chapter {$chapter->chapter_number} Hindi Explanation",
            'description' => $data['description'] ?? "Watch the full Hindi explanation of {$novel->title} Chapter {$chapter->chapter_number}: {$chapter->title}.\n\nSubscribe for daily chapters!",
            'tags' => $data['tags'] ?? ['web novel hindi', "chapter {$chapter->chapter_number}", 'hindi recap'],
        ];
    }
}
