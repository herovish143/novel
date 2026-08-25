<?php

namespace Domain\Visual\Models;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int $script_id
 * @property int $sequence
 * @property int $start_ms
 * @property int $end_ms
 * @property string $scene_type
 * @property string $description
 * @property string $image_prompt
 * @property string $camera_motion
 * @property int $importance
 * @property string $status
 */
class Scene extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'script_id',
        'sequence',
        'start_ms',
        'end_ms',
        'scene_type',
        'description',
        'image_prompt',
        'camera_motion',
        'importance',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'start_ms' => 'integer',
            'end_ms' => 'integer',
            'importance' => 'integer',
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
     * @return BelongsTo<Script, $this>
     */
    public function script(): BelongsTo
    {
        return $this->belongsTo(Script::class);
    }

    /**
     * @return HasMany<SceneAsset, $this>
     */
    public function assets(): HasMany
    {
        return $this->hasMany(SceneAsset::class);
    }
}
