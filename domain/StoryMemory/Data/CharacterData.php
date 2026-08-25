<?php

namespace Domain\StoryMemory\Data;

use Domain\StoryMemory\Models\Character;
use Spatie\LaravelData\Data;

class CharacterData extends Data
{
    /**
     * @param  list<string>  $aliases
     */
    public function __construct(
        public int $id,
        public int $novel_id,
        public string $name,
        public string $canonical_name,
        public ?string $gender,
        public ?string $age_description,
        public ?string $physical_description,
        public ?string $personality,
        public ?string $visual_description,
        public string $importance,
        public array $aliases = [],
    ) {}

    public static function fromModel(Character $character): self
    {
        return new self(
            id: $character->id,
            novel_id: $character->novel_id,
            name: $character->name,
            canonical_name: $character->canonical_name,
            gender: $character->gender,
            age_description: $character->age_description,
            physical_description: $character->physical_description,
            personality: $character->personality,
            visual_description: $character->visual_description,
            importance: $character->importance,
            aliases: $character->aliases->pluck('alias')->toArray(),
        );
    }
}
