<?php

namespace Domain\Publishing\Models;

use Domain\Novel\Models\Chapter;
use Domain\Video\Models\VideoProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int|null $video_project_id
 * @property string $title
 * @property string $description
 * @property array<int, string>|null $tags
 * @property string|null $thumbnail_path
 * @property string $visibility
 * @property string|null $youtube_video_id
 * @property string $publish_status
 * @property Carbon|null $scheduled_at
 */
class YouTubePublication extends Model
{
    use HasFactory;

    protected $table = 'youtube_publications';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'video_project_id',
        'title',
        'description',
        'tags',
        'thumbnail_path',
        'visibility',
        'youtube_video_id',
        'publish_status',
        'scheduled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'scheduled_at' => 'datetime',
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
     * @return BelongsTo<VideoProject, $this>
     */
    public function videoProject(): BelongsTo
    {
        return $this->belongsTo(VideoProject::class);
    }
}
