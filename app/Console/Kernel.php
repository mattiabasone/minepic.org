<?php

declare(strict_types=1);

namespace Minepic\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckUuid::class,
        Commands\CleanAccountsTable::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
    }
}
