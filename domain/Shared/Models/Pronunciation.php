<?php

namespace Domain\Shared\Models;

use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $novel_id
 * @property string $term
 * @property string $pronunciation
 * @property string $language
 * @property string|null $notes
 */
class Pronunciation extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'novel_id',
        'term',
        'pronunciation',
        'language',
        'notes',
    ];

    /**
     * @return BelongsTo<Novel, $this>
     */
    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }
}
