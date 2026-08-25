<?php

namespace Domain\StoryMemory\Data;

use Domain\StoryMemory\Models\Character;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class CharacterData extends Data
{
    /**
     * @param  list<string>  $aliases
     */
    public function __construct(
        public int $id,
        public int $novelId,
        public string $name,
        public string $canonicalName,
        public ?string $gender,
        public ?string $ageDescription,
        public ?string $physicalDescription,
        public ?string $personality,
        public ?string $visualDescription,
        public string $importance,
        public array $aliases = [],
    ) {}

    public static function fromModel(Character $character): self
    {
        return new self(
            id: $character->id,
            novelId: $character->novel_id,
            name: $character->name,
            canonicalName: $character->canonical_name,
            gender: $character->gender,
            ageDescription: $character->age_description,
            physicalDescription: $character->physical_description,
            personality: $character->personality,
            visualDescription: $character->visual_description,
            importance: $character->importance,
            aliases: $character->aliases->pluck('alias')->toArray(),
        );
    }
}
