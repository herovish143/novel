<?php

namespace Domain\StoryMemory\Data;

use Domain\StoryMemory\Models\StoryEvent;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class StoryEventData extends Data
{
    public function __construct(
        public int $id,
        public int $novelId,
        public int $chapterId,
        public int $sequence,
        public string $eventType,
        public string $description,
        public int $importanceScore,
    ) {}

    public static function fromModel(StoryEvent $event): self
    {
        return new self(
            id: $event->id,
            novelId: $event->novel_id,
            chapterId: $event->chapter_id,
            sequence: $event->sequence,
            eventType: $event->event_type,
            description: $event->description,
            importanceScore: $event->importance_score,
        );
    }
}
