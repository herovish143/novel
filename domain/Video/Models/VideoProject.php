<?php

namespace Domain\Video\Models;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int $script_id
 * @property string $resolution
 * @property int $fps
 * @property string $status
 * @property int $duration_ms
 * @property string|null $output_path
 * @property Carbon|null $render_started_at
 * @property Carbon|null $render_completed_at
 * @property float $cost
 */
class VideoProject extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'script_id',
        'resolution',
        'fps',
        'status',
        'duration_ms',
        'output_path',
        'render_started_at',
        'render_completed_at',
        'cost',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fps' => 'integer',
            'duration_ms' => 'integer',
            'cost' => 'float',
            'render_started_at' => 'datetime',
            'render_completed_at' => 'datetime',
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
     * @return HasMany<VideoTimelineItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(VideoTimelineItem::class);
    }
}
