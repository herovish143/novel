<?php

namespace Domain\Novel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int $version
 * @property string $raw_content
 * @property string $clean_content
 * @property string $content_hash
 * @property string $import_method
 * @property string|null $imported_by
 * @property Carbon $created_at
 */
class ChapterSourceVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'chapter_source_versions';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'version',
        'raw_content',
        'clean_content',
        'content_hash',
        'import_method',
        'imported_by',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'created_at' => 'datetime',
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
