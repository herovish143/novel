<?php

namespace Domain\Voice\Models;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int $script_id
 * @property string $format
 * @property string $storage_path
 * @property string $language
 */
class SubtitleFile extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'script_id',
        'format',
        'storage_path',
        'language',
    ];

    /**
     * @return BelongsTo<Chapter, $this>
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * @return BelongsTo<Script, $this>
     */
    public function script(): BelongsTo
    {
        return $this->belongsTo(Script::class);
    }
}
