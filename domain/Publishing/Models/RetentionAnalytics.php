<?php

namespace Domain\Publishing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $youtube_publication_id
 * @property int $views_count
 * @property int $average_view_duration_seconds
 * @property float $completion_percentage
 * @property float $ctr_percentage
 * @property float $revenue_usd
 * @property array<string, mixed>|null $metadata
 */
class RetentionAnalytics extends Model
{
    use HasFactory;

    protected $table = 'retention_analytics';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'youtube_publication_id',
        'views_count',
        'average_view_duration_seconds',
        'completion_percentage',
        'ctr_percentage',
        'revenue_usd',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'views_count' => 'integer',
            'average_view_duration_seconds' => 'integer',
            'completion_percentage' => 'float',
            'ctr_percentage' => 'float',
            'revenue_usd' => 'float',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<YouTubePublication, $this>
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(YouTubePublication::class, 'youtube_publication_id');
    }
}
