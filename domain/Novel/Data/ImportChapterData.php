<?php

declare(strict_types=1);

namespace Domain\Novel\Data;

use Spatie\LaravelData\Data;

class ImportChapterData extends Data
{
    public function __construct(
        public int $chapter_number,
        public string $title,
        public string $source_text,
        public ?string $source_url = null,
    ) {}

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
