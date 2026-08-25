<?php

namespace Domain\StoryMemory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $character_id
 * @property string $alias
 */
class CharacterAlias extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'character_id',
        'alias',
    ];

    /**
     * @return BelongsTo<Character, $this>
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
