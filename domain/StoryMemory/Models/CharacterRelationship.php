<?php

namespace Domain\StoryMemory\Models;

use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $novel_id
 * @property int $source_entity_id
 * @property int $target_entity_id
 * @property string $relationship_type
 * @property string|null $description
 * @property int|null $valid_from_chapter_id
 * @property int|null $valid_to_chapter_id
 */
class CharacterRelationship extends Model
{
    use HasFactory;

    protected $table = 'character_relationships';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'source_entity_id',
        'target_entity_id',
        'relationship_type',
        'description',
        'valid_from_chapter_id',
        'valid_to_chapter_id',
    ];

    /**
     * @return BelongsTo<Novel, $this>
     */
    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * @return BelongsTo<Character, $this>
     */
    public function sourceCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'source_entity_id');
    }

    /**
     * @return BelongsTo<Character, $this>
     */
    public function targetCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'target_entity_id');
    }
}
