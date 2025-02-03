<?php

namespace App\Jobs;

use App\Facades\FileManager;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteExpiredFilesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        File::whereNotNull('expires_in')
            ->where('expires_in', '<=', Carbon::now())
            ->each(fn($file) => FileManager::delete($file));
    }
}
