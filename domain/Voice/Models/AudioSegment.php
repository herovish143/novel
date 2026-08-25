<?php

namespace Domain\Voice\Models;

use Domain\Script\Models\ScriptSegment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $script_segment_id
 * @property string $provider
 * @property string|null $voice_id
 * @property string $model
 * @property string $storage_path
 * @property int $duration_ms
 * @property int $character_count
 * @property float $cost
 * @property string $status
 * @property array<string, mixed>|null $metadata
 */
class AudioSegment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'script_segment_id',
        'provider',
        'voice_id',
        'model',
        'storage_path',
        'duration_ms',
        'character_count',
        'cost',
        'status',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_ms' => 'integer',
            'character_count' => 'integer',
            'cost' => 'float',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ScriptSegment, $this>
     */
    public function segment(): BelongsTo
    {
        return $this->belongsTo(ScriptSegment::class, 'script_segment_id');
    }
}
