<?php

namespace Domain\Publishing\Jobs;

use Domain\Publishing\Models\YouTubePublication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoPublishSchedulerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $publications = YouTubePublication::where('publish_status', 'UPLOADED')
            ->where('visibility', 'UNLISTED')
            ->get();

        foreach ($publications as $publication) {
            $publication->update([
                'visibility' => 'PUBLIC',
                'publish_status' => 'PUBLISHED',
            ]);

            $publication->chapter?->update(['status' => 'PUBLISHED']);
        }
    }
}
