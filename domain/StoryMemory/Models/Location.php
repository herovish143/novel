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
 * @property string|null $visual_description
 * @property int|null $first_chapter_id
 * @property int|null $last_seen_chapter_id
 * @property array<string, mixed>|null $metadata
 */
class Location extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'name',
        'description',
        'visual_description',
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
}
