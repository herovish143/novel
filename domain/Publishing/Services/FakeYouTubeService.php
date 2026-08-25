<?php

declare(strict_types=1);

namespace Domain\Publishing\Services;

use Domain\Publishing\Data\YouTubeUploadData;
use Domain\Publishing\Data\YouTubeUploadResultData;

class FakeYouTubeService implements YouTubeService
{
    public function upload(YouTubeUploadData $data): YouTubeUploadResultData
    {
        $videoId = 'yt_demo_'.substr(md5($data->title), 0, 10);

        return new YouTubeUploadResultData(
            youtubeVideoId: $videoId,
            videoUrl: "https://www.youtube.com/watch?v={$videoId}",
            status: 'UPLOADED'
        );
    }
}
