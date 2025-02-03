<?php

use App\Jobs\DeleteExpiredFilesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Job para deletar arquivos expirados
Schedule::job(new DeleteExpiredFilesJob())->dailyAt('00:00');