<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\SyncAlmacenDigital::class,
        Commands\SyncAlmacenDigitalImages::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $filename = "./crontab.log";
        // $schedule->command('inspire')
        //     ->hourly();
        $schedule->command('sync:AlmacenDigital')
            ->daily()->sendOutputTo($filename);
        $schedule->command('sync:AlmacenDigitalImages')
            ->everyMinute()->withoutOverlapping()->sendOutputTo($filename);
    }
}
