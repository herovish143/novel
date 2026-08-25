<?php

declare(strict_types=1);

namespace Domain\Publishing\Services;

use Domain\Publishing\Data\YouTubeUploadData;
use Domain\Publishing\Data\YouTubeUploadResultData;

interface YouTubeService
{
    public function upload(YouTubeUploadData $data): YouTubeUploadResultData;
}
