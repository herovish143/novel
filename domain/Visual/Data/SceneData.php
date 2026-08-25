<?php

namespace Domain\Visual\Data;

use Domain\Visual\Models\Scene;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;

class SceneData extends Data
{
    public function __construct(
        public int $id,
        public int $chapter_id,
        public int $script_id,
        public int $sequence,
        public int $start_ms,
        public int $end_ms,
        public string $scene_type,
        public string $description,
        public string $image_prompt,
        public string $camera_motion,
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
            chapter_id: $scene->chapter_id,
            script_id: $scene->script_id,
            sequence: $scene->sequence,
            start_ms: $scene->start_ms,
            end_ms: $scene->end_ms,
            scene_type: $scene->scene_type,
            description: $scene->description,
            image_prompt: $scene->image_prompt,
            camera_motion: $scene->camera_motion,
            importance: $scene->importance,
            status: $scene->status,
            imageUrl: $imageUrl,
        );
    }
}
