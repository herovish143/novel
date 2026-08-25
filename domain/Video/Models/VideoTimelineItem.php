<?php

namespace Domain\Video\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $video_project_id
 * @property int $sequence
 * @property string $type
 * @property int $start_ms
 * @property int $end_ms
 * @property int|null $asset_id
 * @property string $transition
 * @property string $animation
 * @property array<string, mixed>|null $metadata
 */
class VideoTimelineItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'video_project_id',
        'sequence',
        'type',
        'start_ms',
        'end_ms',
        'asset_id',
        'transition',
        'animation',
        'metadata',
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
            'asset_id' => 'integer',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<VideoProject, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(VideoProject::class, 'video_project_id');
    }
}
