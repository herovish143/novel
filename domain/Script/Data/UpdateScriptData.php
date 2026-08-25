<?php

declare(strict_types=1);

namespace Domain\Script\Data;

use Spatie\LaravelData\Data;

class UpdateScriptData extends Data
{
    public function __construct(
        public ?string $hook = null,
        public ?string $previous_recap = null,
        public ?string $main_narration = null,
        public ?string $analysis = null,
        public ?string $ending_hook = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'hook' => ['nullable', 'string'],
            'previous_recap' => ['nullable', 'string'],
            'main_narration' => ['nullable', 'string'],
            'analysis' => ['nullable', 'string'],
            'ending_hook' => ['nullable', 'string'],
        ];
    }
}
