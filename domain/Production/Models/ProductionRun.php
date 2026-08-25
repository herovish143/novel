<?php

namespace Domain\Production\Models;

use Domain\Billing\Models\AiUsage;
use Domain\Novel\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chapter_id
 * @property string $status
 * @property string $current_stage
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property float $estimated_cost
 * @property float $actual_cost
 * @property string|null $error
 */
class ProductionRun extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'status',
        'current_stage',
        'started_at',
        'completed_at',
        'estimated_cost',
        'actual_cost',
        'error',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_cost' => 'float',
            'actual_cost' => 'float',
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
     * @return HasMany<ProductionStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ProductionStep::class);
    }

    /**
     * @return HasMany<AiUsage, $this>
     */
    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }
}
