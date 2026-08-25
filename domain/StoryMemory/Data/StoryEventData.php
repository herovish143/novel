<?php

namespace Domain\StoryMemory\Data;

use Domain\StoryMemory\Models\StoryEvent;
use Spatie\LaravelData\Data;

class StoryEventData extends Data
{
    public function __construct(
        public int $id,
        public int $novel_id,
        public int $chapter_id,
        public int $sequence,
        public string $event_type,
        public string $description,
        public int $importance_score,
    ) {}

    public static function fromModel(StoryEvent $event): self
    {
        return new self(
            id: $event->id,
            novel_id: $event->novel_id,
            chapter_id: $event->chapter_id,
            sequence: $event->sequence,
            event_type: $event->event_type,
            description: $event->description,
            importance_score: $event->importance_score,
        );
    }
}
