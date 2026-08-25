<?php

declare(strict_types=1);

namespace Domain\Script\Data;

use Domain\Script\Models\Script;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class UpdateScriptData extends Data
{
    public function __construct(
        public ?string $hook = null,
        public ?string $previousRecap = null,
        public ?string $mainNarration = null,
        public ?string $analysis = null,
        public ?string $endingHook = null,
    ) {}

    public static function fromModel(Script $script): self
    {
        return new self(
            hook: $script->hook,
            previousRecap: $script->previous_recap,
            mainNarration: $script->main_narration,
            analysis: $script->analysis,
            endingHook: $script->ending_hook,
        );
    }

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
