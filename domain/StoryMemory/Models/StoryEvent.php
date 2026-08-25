<?php

namespace Domain\StoryMemory\Models;

use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $novel_id
 * @property int $chapter_id
 * @property int $sequence
 * @property string $event_type
 * @property string $description
 * @property int $importance_score
 * @property int|null $location_id
 * @property array<string, mixed>|null $metadata
 */
class StoryEvent extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'chapter_id',
        'sequence',
        'event_type',
        'description',
        'importance_score',
        'location_id',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'importance_score' => 'integer',
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
     * @return BelongsTo<Chapter, $this>
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
