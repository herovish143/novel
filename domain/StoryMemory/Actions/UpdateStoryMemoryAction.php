<?php

namespace Domain\StoryMemory\Actions;

use Domain\Novel\Models\Chapter;
use Domain\StoryMemory\Models\Ability;
use Domain\StoryMemory\Models\ChapterSummary;
use Domain\StoryMemory\Models\Character;
use Domain\StoryMemory\Models\CharacterAlias;
use Domain\StoryMemory\Models\CharacterRelationship;
use Domain\StoryMemory\Models\Location;
use Domain\StoryMemory\Models\StoryEvent;
use Domain\StoryMemory\Models\StoryItem;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStoryMemoryAction
{
    use AsAction;

    /**
     * Persist extracted chapter facts into normalized story memory models.
     *
     * @param  array<string, mixed>  $facts
     */
    public function handle(Chapter $chapter, array $facts): void
    {
        $novel = $chapter->novel;

        // 1. Chapter Summary
        ChapterSummary::updateOrCreate(
            ['chapter_id' => $chapter->id],
            [
                'summary' => $facts['summary'] ?? '',
                'important_reveals' => $facts['important_reveals'] ?? [],
                'unresolved_questions' => $facts['unresolved_questions'] ?? [],
                'ai_model' => 'gpt-4o',
                'prompt_version' => 'v1',
            ]
        );

        // 2. Characters & Aliases
        $characterMap = [];
        foreach ($facts['characters'] ?? [] as $charData) {
            $canonicalName = $charData['canonical_name'] ?? $charData['name'];

            $character = Character::query()
                ->where('novel_id', $novel->id)
                ->where(function ($query) use ($canonicalName, $charData): void {
                    $query->where('canonical_name', $canonicalName)
                        ->orWhere('name', $charData['name'])
                        ->orWhereHas('aliases', fn ($q) => $q->where('alias', $charData['name']));
                })
                ->first();

            if (! $character) {
                $character = Character::create([
                    'novel_id' => $novel->id,
                    'name' => $charData['name'],
                    'canonical_name' => $canonicalName,
                    'gender' => $charData['gender'] ?? null,
                    'age_description' => $charData['age_description'] ?? null,
                    'physical_description' => $charData['physical_description'] ?? null,
                    'personality' => $charData['personality'] ?? null,
                    'visual_description' => $charData['visual_description'] ?? null,
                    'importance' => $charData['importance'] ?? 'SECONDARY',
                    'first_chapter_id' => $chapter->id,
                    'last_seen_chapter_id' => $chapter->id,
                ]);
            } else {
                $character->update([
                    'last_seen_chapter_id' => $chapter->id,
                    'physical_description' => $charData['physical_description'] ?? $character->physical_description,
                    'personality' => $charData['personality'] ?? $character->personality,
                    'visual_description' => $charData['visual_description'] ?? $character->visual_description,
                ]);
            }

            if ($charData['name'] !== $canonicalName) {
                CharacterAlias::firstOrCreate([
                    'character_id' => $character->id,
                    'alias' => $charData['name'],
                ]);
            }

            $characterMap[$charData['name']] = $character;
            $characterMap[$canonicalName] = $character;
        }

        // 3. Locations
        foreach ($facts['locations'] ?? [] as $locData) {
            $location = Location::firstOrCreate(
                ['novel_id' => $novel->id, 'name' => $locData['name']],
                [
                    'description' => $locData['description'] ?? null,
                    'visual_description' => $locData['visual_description'] ?? null,
                    'first_chapter_id' => $chapter->id,
                    'last_seen_chapter_id' => $chapter->id,
                ]
            );

            $location->update(['last_seen_chapter_id' => $chapter->id]);
        }

        // 4. Abilities
        foreach ($facts['abilities'] ?? [] as $abilityData) {
            $owner = null;
            if (! empty($abilityData['character_name'])) {
                $owner = $characterMap[$abilityData['character_name']] ?? null;
            }

            Ability::firstOrCreate(
                ['novel_id' => $novel->id, 'name' => $abilityData['name']],
                [
                    'character_id' => $owner?->id,
                    'description' => $abilityData['description'] ?? null,
                    'first_chapter_id' => $chapter->id,
                    'last_updated_chapter_id' => $chapter->id,
                ]
            );
        }

        // 5. Items
        foreach ($facts['items'] ?? [] as $itemData) {
            $owner = null;
            if (! empty($itemData['owner_character_name'])) {
                $owner = $characterMap[$itemData['owner_character_name']] ?? null;
            }

            StoryItem::firstOrCreate(
                ['novel_id' => $novel->id, 'name' => $itemData['name']],
                [
                    'description' => $itemData['description'] ?? null,
                    'owner_character_id' => $owner?->id,
                    'first_chapter_id' => $chapter->id,
                    'last_seen_chapter_id' => $chapter->id,
                ]
            );
        }

        // 6. Events
        foreach ($facts['events'] ?? [] as $eventData) {
            StoryEvent::create([
                'novel_id' => $novel->id,
                'chapter_id' => $chapter->id,
                'sequence' => $eventData['sequence'] ?? 1,
                'event_type' => $eventData['event_type'] ?? 'PLOT',
                'description' => $eventData['description'],
                'importance_score' => $eventData['importance_score'] ?? 5,
            ]);
        }

        // 7. Relationships
        foreach ($facts['relationships_changed'] ?? [] as $relData) {
            $c1 = $characterMap[$relData['character_name']] ?? null;
            $c2 = $characterMap[$relData['related_character_name']] ?? null;

            if ($c1 && $c2) {
                CharacterRelationship::updateOrCreate(
                    [
                        'novel_id' => $novel->id,
                        'character_id' => $c1->id,
                        'related_character_id' => $c2->id,
                    ],
                    [
                        'relationship_type' => $relData['relationship_type'],
                        'description' => $relData['description'] ?? null,
                        'first_chapter_id' => $chapter->id,
                        'last_updated_chapter_id' => $chapter->id,
                    ]
                );
            }
        }

        $chapter->update(['status' => 'MEMORY_UPDATED']);
    }
}
