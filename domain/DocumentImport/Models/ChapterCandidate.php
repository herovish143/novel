<?php

namespace Domain\DocumentImport\Models;

use Database\Factories\ChapterCandidateFactory;
use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\Novel\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterCandidate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): ChapterCandidateFactory
    {
        return ChapterCandidateFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => ChapterCandidateStatus::class,
            'approved_at' => 'datetime',
            'imported_at' => 'datetime',
        ];
    }

    public function documentImport(): BelongsTo
    {
        return $this->belongsTo(DocumentImport::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
