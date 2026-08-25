<?php

namespace Domain\Visual\Services;

use Domain\Novel\Models\Novel;

class ScenePromptBuilder
{
    /**
     * Build detailed DALL-E 3 prompt combining novel style, character profiles, location, and scene action.
     *
     * @param  array<string, mixed>  $sceneData
     */
    public function buildPrompt(Novel $novel, array $sceneData): string
    {
        $visualStyle = $novel->visual_style ?: 'dark cinematic fantasy';
        $location = $sceneData['location'] ?? 'fantasy realm background';
        $action = $sceneData['description'] ?? 'cinematic shot';
        $characters = $sceneData['characters'] ?? [];

        $characterDesc = 'No main character present';
        if (! empty($characters)) {
            $charList = implode(', ', $characters);
            $characterDesc = "Character focus: {$charList}";
        }

        return <<<PROMPT
{$visualStyle}, high detail, 16:9 widescreen composition, epic lighting, cinematic atmosphere.

Location: {$location}
{$characterDesc}
Scene Action: {$action}

Style Guidelines: High resolution, no text, no captions, highly immersive fantasy art.
PROMPT;
    }
}
