<?php

namespace Domain\Novel\Models;

use Database\Factories\NovelFactory;
use Domain\DocumentImport\Models\DocumentImport;
use Domain\Shared\Models\Pronunciation;
use Domain\StoryMemory\Models\Ability;
use Domain\StoryMemory\Models\Character;
use Domain\StoryMemory\Models\Location;
use Domain\StoryMemory\Models\StoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $original_language
 * @property string $output_language
 * @property string|null $source_url
 * @property string|null $description
 * @property string|null $default_voice_provider
 * @property string|null $default_voice_id
 * @property string $visual_style
 * @property string $narration_style
 * @property float $max_cost_per_episode
 * @property string $status
 */
class Novel extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): NovelFactory
    {
        return NovelFactory::new();
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'original_language',
        'output_language',
        'source_url',
        'description',
        'default_voice_provider',
        'default_voice_id',
        'visual_style',
        'narration_style',
        'max_cost_per_episode',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_cost_per_episode' => 'float',
        ];
    }

    /**
     * @return HasMany<Chapter, $this>
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * @return HasMany<DocumentImport, $this>
     */
    public function documentImports(): HasMany
    {
        return $this->hasMany(DocumentImport::class);
    }

    /**
     * @return HasMany<Character, $this>
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    /**
     * @return HasMany<Location, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return HasMany<Ability, $this>
     */
    public function abilities(): HasMany
    {
        return $this->hasMany(Ability::class);
    }

    /**
     * @return HasMany<StoryItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(StoryItem::class);
    }

    /**
     * @return HasMany<Pronunciation, $this>
     */
    public function pronunciations(): HasMany
    {
        return $this->hasMany(Pronunciation::class);
    }
}
