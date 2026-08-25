<?php

namespace Domain\Visual\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $scene_id
 * @property string $asset_type
 * @property string $provider
 * @property string $prompt
 * @property string $storage_path
 * @property int $width
 * @property int $height
 * @property float $cost
 * @property string $status
 * @property array<string, mixed>|null $metadata
 */
class SceneAsset extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'scene_id',
        'asset_type',
        'provider',
        'prompt',
        'storage_path',
        'width',
        'height',
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
            'width' => 'integer',
            'height' => 'integer',
            'cost' => 'float',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Scene, $this>
     */
    public function scene(): BelongsTo
    {
        return $this->belongsTo(Scene::class);
    }
}
