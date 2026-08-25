<?php

namespace Domain\Script\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $script_id
 * @property int $sequence
 * @property string $type
 * @property string $text
 * @property float|null $estimated_duration
 * @property string $status
 */
class ScriptSegment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'script_id',
        'sequence',
        'type',
        'text',
        'estimated_duration',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'estimated_duration' => 'float',
        ];
    }

    /**
     * @return BelongsTo<Script, $this>
     */
    public function script(): BelongsTo
    {
        return $this->belongsTo(Script::class);
    }
}
