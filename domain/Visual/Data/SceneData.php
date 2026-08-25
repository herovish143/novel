<?php

namespace Domain\Visual\Data;

use Domain\Visual\Models\Scene;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class SceneData extends Data
{
    public function __construct(
        public int $id,
        public int $chapterId,
        public int $scriptId,
        public int $sequence,
        public int $startMs,
        public int $endMs,
        public string $sceneType,
        public string $description,
        public string $imagePrompt,
        public string $cameraMotion,
        public int $importance,
        public string $status,
        public ?string $imageUrl = null,
    ) {}

    public static function fromModel(Scene $scene): self
    {
        $primaryAsset = $scene->assets->first();
        $imageUrl = null;

        if ($primaryAsset && Storage::disk('public')->exists($primaryAsset->storage_path)) {
            $imageUrl = Storage::disk('public')->url($primaryAsset->storage_path);
        }

        return new self(
            id: $scene->id,
            chapterId: $scene->chapter_id,
            scriptId: $scene->script_id,
            sequence: $scene->sequence,
            startMs: $scene->start_ms,
            endMs: $scene->end_ms,
            sceneType: $scene->scene_type,
            description: $scene->description,
            imagePrompt: $scene->image_prompt,
            cameraMotion: $scene->camera_motion,
            importance: $scene->importance,
            status: $scene->status,
            imageUrl: $imageUrl,
        );
    }
}
