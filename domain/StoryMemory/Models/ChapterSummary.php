<?php

namespace Domain\StoryMemory\Models;

use Domain\Novel\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chapter_id
 * @property string $summary
 * @property array<string, mixed>|null $important_reveals
 * @property array<string, mixed>|null $unresolved_questions
 * @property string|null $continuity_notes
 * @property string|null $ai_model
 * @property string|null $prompt_version
 */
class ChapterSummary extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'summary',
        'important_reveals',
        'unresolved_questions',
        'continuity_notes',
        'ai_model',
        'prompt_version',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'important_reveals' => 'array',
            'unresolved_questions' => 'array',
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
