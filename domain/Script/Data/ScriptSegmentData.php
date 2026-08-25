<?php

namespace Domain\Script\Data;

use Domain\Script\Models\ScriptSegment;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ScriptSegmentData extends Data
{
    public function __construct(
        public int $id,
        public int $scriptId,
        public int $sequence,
        public string $type,
        public string $text,
        public ?float $estimatedDuration,
        public string $status,
    ) {}

    public static function fromModel(ScriptSegment $segment): self
    {
        return new self(
            id: $segment->id,
            scriptId: $segment->script_id,
            sequence: $segment->sequence,
            type: $segment->type,
            text: $segment->text,
            estimatedDuration: $segment->estimated_duration,
            status: $segment->status,
        );
    }
}
