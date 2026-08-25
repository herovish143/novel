<?php

namespace Domain\Script\Data;

use Domain\Script\Models\Script;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ScriptData extends Data
{
    /**
     * @param  list<ScriptSegmentData>  $segments
     */
    public function __construct(
        public int $id,
        public int $chapterId,
        public int $version,
        public string $language,
        public string $status,
        public ?string $hook,
        public ?string $previousRecap,
        public ?string $mainNarration,
        public ?string $analysis,
        public ?string $endingHook,
        public string $fullScript,
        public int $wordCount,
        public int $characterCount,
        public array $segments = [],
    ) {}

    public static function fromModel(Script $script): self
    {
        return new self(
            id: $script->id,
            chapterId: $script->chapter_id,
            version: $script->version,
            language: $script->language,
            status: $script->status,
            hook: $script->hook,
            previousRecap: $script->previous_recap,
            mainNarration: $script->main_narration,
            analysis: $script->analysis,
            endingHook: $script->ending_hook,
            fullScript: $script->full_script,
            wordCount: $script->word_count,
            characterCount: $script->character_count,
            segments: ScriptSegmentData::collect($script->segments)->toArray(),
        );
    }
}
