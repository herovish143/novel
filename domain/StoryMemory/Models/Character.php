<?php

namespace Domain\StoryMemory\Models;

use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $novel_id
 * @property string $name
 * @property string $canonical_name
 * @property string|null $gender
 * @property string|null $age_description
 * @property string|null $physical_description
 * @property string|null $personality
 * @property string|null $background
 * @property string|null $visual_description
 * @property string $importance
 * @property int|null $first_chapter_id
 * @property int|null $last_seen_chapter_id
 * @property array<string, mixed>|null $metadata
 */
class Character extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'name',
        'canonical_name',
        'gender',
        'age_description',
        'physical_description',
        'personality',
        'background',
        'visual_description',
        'importance',
        'first_chapter_id',
        'last_seen_chapter_id',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Novel, $this>
     */
    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * @return HasMany<CharacterAlias, $this>
     */
    public function aliases(): HasMany
    {
        return $this->hasMany(CharacterAlias::class);
    }

    /**
     * @return HasMany<CharacterRelationship, $this>
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(CharacterRelationship::class, 'character_id');
    }

    /**
     * @return HasMany<Ability, $this>
     */
    public function abilities(): HasMany
    {
        return $this->hasMany(Ability::class);
    }
}
