<?php

namespace Domain\Script\Data;

use Domain\Script\Models\Script;
use Spatie\LaravelData\Data;

class ScriptData extends Data
{
    /**
     * @param  list<ScriptSegmentData>  $segments
     */
    public function __construct(
        public int $id,
        public int $chapter_id,
        public int $version,
        public string $language,
        public string $status,
        public ?string $hook,
        public ?string $previous_recap,
        public ?string $main_narration,
        public ?string $analysis,
        public ?string $ending_hook,
        public string $full_script,
        public int $word_count,
        public int $character_count,
        public array $segments = [],
    ) {}

    public static function fromModel(Script $script): self
    {
        return new self(
            id: $script->id,
            chapter_id: $script->chapter_id,
            version: $script->version,
            language: $script->language,
            status: $script->status,
            hook: $script->hook,
            previous_recap: $script->previous_recap,
            main_narration: $script->main_narration,
            analysis: $script->analysis,
            ending_hook: $script->ending_hook,
            full_script: $script->full_script,
            word_count: $script->word_count,
            character_count: $script->character_count,
            segments: ScriptSegmentData::collect($script->segments)->toArray(),
        );
    }
}
