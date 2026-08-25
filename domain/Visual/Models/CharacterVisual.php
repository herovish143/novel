<?php

namespace Domain\Visual\Models;

use Domain\StoryMemory\Models\Character;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $character_id
 * @property string $image_path
 * @property string|null $prompt
 * @property string $provider
 * @property bool $is_primary
 * @property Carbon|null $approved_at
 */
class CharacterVisual extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'character_id',
        'image_path',
        'prompt',
        'provider',
        'is_primary',
        'approved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Character, $this>
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
