<?php

namespace Domain\Billing\Models;

use Domain\Production\Models\ProductionRun;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $production_run_id
 * @property string $provider
 * @property string $service
 * @property string|null $model
 * @property int $input_tokens
 * @property int $output_tokens
 * @property int $characters
 * @property int $images
 * @property float $seconds
 * @property float $estimated_cost
 * @property float $actual_cost
 * @property array<string, mixed>|null $metadata
 */
class AiUsage extends Model
{
    use HasFactory;

    protected $table = 'ai_usage';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'production_run_id',
        'provider',
        'service',
        'model',
        'input_tokens',
        'output_tokens',
        'characters',
        'images',
        'seconds',
        'estimated_cost',
        'actual_cost',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'characters' => 'integer',
            'images' => 'integer',
            'seconds' => 'float',
            'estimated_cost' => 'float',
            'actual_cost' => 'float',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ProductionRun, $this>
     */
    public function productionRun(): BelongsTo
    {
        return $this->belongsTo(ProductionRun::class);
    }
}
