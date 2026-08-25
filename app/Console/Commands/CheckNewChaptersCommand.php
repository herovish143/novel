<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Domain\Production\Services\ScheduledChapterChecker;
use Illuminate\Console\Command;

class CheckNewChaptersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'novel:check-chapters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically check active web novels for newly released chapters and trigger pipeline';

    /**
     * Execute the console command.
     */
    public function handle(ScheduledChapterChecker $checker): int
    {
        $this->info('Scanning web novels for new chapter releases...');

        $imported = $checker->checkAll();

        $this->info("Imported and queued {$imported} new chapter(s).");

        return Command::SUCCESS;
    }
}
