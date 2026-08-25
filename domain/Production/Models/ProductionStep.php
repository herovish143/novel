<?php

namespace Domain\Production\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $production_run_id
 * @property string $stage
 * @property string $status
 * @property int $attempts
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property string|null $error
 * @property array<string, mixed>|null $metadata
 */
class ProductionStep extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'production_run_id',
        'stage',
        'status',
        'attempts',
        'started_at',
        'completed_at',
        'error',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ProductionRun, $this>
     */
    public function run(): BelongsTo
    {
        return $this->belongsTo(ProductionRun::class, 'production_run_id');
    }
}
