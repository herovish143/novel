<?php

namespace Domain\Publishing\Actions;

use Domain\Novel\Models\Chapter;
use Domain\Novel\Services\RightsManagementGate;
use Domain\Publishing\Data\YouTubeUploadData;
use Domain\Publishing\Models\YouTubePublication;
use Domain\Publishing\Services\ThumbnailPipeline;
use Domain\Publishing\Services\YouTubeMetadataGenerator;
use Domain\Publishing\Services\YouTubeService;
use Domain\Video\Models\VideoProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class PublishYouTubeVideoAction
{
    use AsAction;

    public function handle(
        Chapter $chapter,
        YouTubeMetadataGenerator $metadataGenerator,
        ThumbnailPipeline $thumbnailPipeline,
        YouTubeService $youTubeService,
        ?RightsManagementGate $rightsGate = null
    ): YouTubePublication {
        $rightsGate = $rightsGate ?? resolve(RightsManagementGate::class);
        $rightsGate->authorize($chapter->novel);

        $videoProject = VideoProject::where('chapter_id', $chapter->id)->latest()->first();

        // 1. Generate Metadata
        $metadata = $metadataGenerator->generate($chapter);

        // 2. Generate Thumbnail
        $thumbnailPath = $thumbnailPipeline->generate($chapter);

        // 3. Upload to YouTube API
        $videoFilePath = Storage::disk('public')->path("novels/{$chapter->novel->slug}/chapters/{$chapter->chapter_number}/video/final.mp4");

        $uploadData = new YouTubeUploadData(
            title: $metadata['title'],
            description: $metadata['description'],
            tags: $metadata['tags'],
            videoFilePath: $videoFilePath,
            thumbnailPath: Storage::disk('public')->path($thumbnailPath),
            visibility: 'UNLISTED'
        );

        $result = $youTubeService->upload($uploadData);

        // 4. Save Publication Record
        $publication = YouTubePublication::create([
            'chapter_id' => $chapter->id,
            'video_project_id' => $videoProject?->id,
            'title' => $metadata['title'],
            'description' => $metadata['description'],
            'tags' => $metadata['tags'],
            'thumbnail_path' => $thumbnailPath,
            'visibility' => 'UNLISTED',
            'youtube_video_id' => $result->youtubeVideoId,
            'publish_status' => 'UPLOADED',
        ]);

        $chapter->update(['status' => 'PUBLISHED']);

        return $publication;
    }

    public function asController(
        Chapter $chapter,
        YouTubeMetadataGenerator $metadataGenerator,
        ThumbnailPipeline $thumbnailPipeline,
        YouTubeService $youTubeService,
        ?RightsManagementGate $rightsGate = null
    ): RedirectResponse {
        $this->handle($chapter, $metadataGenerator, $thumbnailPipeline, $youTubeService, $rightsGate);

        return to_route('chapters.show', $chapter->id)->with('success', 'Video published to YouTube as UNLISTED draft.');
    }
}
