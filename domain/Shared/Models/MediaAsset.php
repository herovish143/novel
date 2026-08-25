<?php

namespace Domain\Shared\Models;

use Domain\Novel\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chapter_id
 * @property string $type
 * @property int $version
 * @property string $storage_disk
 * @property string $storage_path
 * @property string|null $mime_type
 * @property int $size
 * @property int|null $duration_ms
 * @property int|null $width
 * @property int|null $height
 * @property string|null $checksum
 * @property array<string, mixed>|null $metadata
 * @property string $status
 */
class MediaAsset extends Model
{
    use HasFactory;

    protected $table = 'media_assets';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'type',
        'version',
        'storage_disk',
        'storage_path',
        'mime_type',
        'size',
        'duration_ms',
        'width',
        'height',
        'checksum',
        'metadata',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'size' => 'integer',
            'duration_ms' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Chapter, $this>
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
