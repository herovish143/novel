<?php

namespace Domain\Novel\Models;

use Database\Factories\ChapterFactory;
use Domain\Production\Models\ProductionRun;
use Domain\Script\Models\Script;
use Domain\StoryMemory\Models\ChapterSummary;
use Domain\StoryMemory\Models\StoryEvent;
use Domain\Visual\Models\Scene;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $novel_id
 * @property int $chapter_number
 * @property string $title
 * @property string|null $source_url
 * @property string $source_text
 * @property string $source_hash
 * @property int|null $previous_chapter_id
 * @property string $status
 * @property Carbon|null $imported_at
 * @property Carbon|null $analyzed_at
 * @property Carbon|null $scripted_at
 */
class Chapter extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ChapterFactory
    {
        return ChapterFactory::new();
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'chapter_number',
        'title',
        'source_url',
        'source_text',
        'source_hash',
        'previous_chapter_id',
        'status',
        'imported_at',
        'analyzed_at',
        'scripted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'chapter_number' => 'integer',
            'imported_at' => 'datetime',
            'analyzed_at' => 'datetime',
            'scripted_at' => 'datetime',
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
    public function previousChapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'previous_chapter_id');
    }

    /**
     * @return HasOne<ChapterSummary, $this>
     */
    public function summary(): HasOne
    {
        return $this->hasOne(ChapterSummary::class);
    }

    /**
     * @return HasMany<StoryEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(StoryEvent::class);
    }

    /**
     * @return HasMany<Script, $this>
     */
    public function scripts(): HasMany
    {
        return $this->hasMany(Script::class);
    }

    /**
     * @return HasOne<Script, $this>
     */
    public function latestScript(): HasOne
    {
        return $this->hasOne(Script::class)->latestOfMany('version');
    }

    /**
     * @return HasMany<Scene, $this>
     */
    public function scenes(): HasMany
    {
        return $this->hasMany(Scene::class);
    }

    /**
     * @return HasMany<ProductionRun, $this>
     */
    public function productionRuns(): HasMany
    {
        return $this->hasMany(ProductionRun::class);
    }
}
