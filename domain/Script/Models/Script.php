<?php

namespace Domain\Script\Models;

use App\Models\User;
use Domain\Novel\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chapter_id
 * @property int $version
 * @property string $language
 * @property string $status
 * @property string|null $hook
 * @property string|null $previous_recap
 * @property string|null $main_narration
 * @property string|null $analysis
 * @property string|null $ending_hook
 * @property string $full_script
 * @property int $word_count
 * @property int $character_count
 * @property string|null $ai_model
 * @property string|null $prompt_version
 * @property Carbon|null $approved_at
 * @property int|null $approved_by
 */
class Script extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'version',
        'language',
        'status',
        'hook',
        'previous_recap',
        'main_narration',
        'analysis',
        'ending_hook',
        'full_script',
        'word_count',
        'character_count',
        'ai_model',
        'prompt_version',
        'approved_at',
        'approved_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'word_count' => 'integer',
            'character_count' => 'integer',
            'approved_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<ScriptSegment, $this>
     */
    public function segments(): HasMany
    {
        return $this->hasMany(ScriptSegment::class);
    }
}
