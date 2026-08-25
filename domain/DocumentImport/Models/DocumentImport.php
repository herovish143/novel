<?php

namespace Domain\DocumentImport\Models;

use Database\Factories\DocumentImportFactory;
use Domain\DocumentImport\Enums\DocumentImportStatus;
use Domain\DocumentImport\Enums\ExtractionMethod;
use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentImport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): DocumentImportFactory
    {
        return DocumentImportFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => DocumentImportStatus::class,
            'extraction_method' => ExtractionMethod::class,
            'metadata' => 'array',
        ];
    }

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(ChapterCandidate::class)->orderBy('sequence');
    }
}
