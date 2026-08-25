<?php

declare(strict_types=1);

namespace Domain\Novel\Data;

use Domain\Novel\Models\Chapter;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ImportChapterData extends Data
{
    public function __construct(
        public int $chapterNumber,
        public string $title,
        public string $sourceText,
        public ?string $sourceUrl = null,
    ) {}

    public static function fromModel(Chapter $chapter): self
    {
        return new self(
            chapterNumber: $chapter->chapter_number,
            title: $chapter->title,
            sourceText: $chapter->source_text,
            sourceUrl: $chapter->source_url,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'chapter_number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'source_text' => ['required', 'string'],
            'source_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
