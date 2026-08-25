<?php

namespace Domain\Script\Data;

use Domain\Script\Models\ScriptSegment;
use Spatie\LaravelData\Data;

class ScriptSegmentData extends Data
{
    public function __construct(
        public int $id,
        public int $script_id,
        public int $sequence,
        public string $type,
        public string $text,
        public ?float $estimated_duration,
        public string $status,
    ) {}

    public static function fromModel(ScriptSegment $segment): self
    {
        return new self(
            id: $segment->id,
            script_id: $segment->script_id,
            sequence: $segment->sequence,
            type: $segment->type,
            text: $segment->text,
            estimated_duration: $segment->estimated_duration,
            status: $segment->status,
        );
    }
}
