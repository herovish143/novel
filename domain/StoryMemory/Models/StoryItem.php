<?php

namespace Domain\StoryMemory\Models;

use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $novel_id
 * @property string $name
 * @property string|null $description
 * @property int|null $owner_character_id
 * @property int|null $first_chapter_id
 * @property int|null $last_seen_chapter_id
 * @property array<string, mixed>|null $metadata
 */
class StoryItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'name',
        'description',
        'owner_character_id',
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
     * @return BelongsTo<Character, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'owner_character_id');
    }
}
