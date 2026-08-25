<?php

namespace Domain\StoryMemory\Models;

use Domain\Novel\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chapter_id
 * @property string $fact_type
 * @property int|null $subject_entity_id
 * @property int|null $object_entity_id
 * @property string $statement
 * @property float $confidence
 * @property string|null $source_reference
 * @property bool $is_verified
 */
class ChapterFact extends Model
{
    use HasFactory;

    protected $table = 'chapter_facts';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'fact_type',
        'subject_entity_id',
        'object_entity_id',
        'statement',
        'confidence',
        'source_reference',
        'is_verified',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Chapter, $this>
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * @return BelongsTo<Character, $this>
     */
    public function subjectCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'subject_entity_id');
    }

    /**
     * @return BelongsTo<Character, $this>
     */
    public function objectCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'object_entity_id');
    }
}
