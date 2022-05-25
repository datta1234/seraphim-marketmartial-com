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
            \App\Console\Commands\GenOrgAction::class,
            \App\Console\Commands\PlaceUserMarketsOnHold::class,
            \App\Console\Commands\UpdateUuid::class,
            \App\Console\Commands\ClearUserSessions::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('gen:actions')->daily();
        $schedule->command('usermarkets:place-on-hold')->everyMinute();
        $schedule->command('users:clear-user-sessions')->everyMinute();

        // Hard reset Org UUID's to keep inconsistant websocket disconnect from happening
        $schedule->call(function() {
            \Log::info("RESET ORG UUIDs");
            \App\Helpers\Misc\ResolveUuid::generateOrganisationUuid();
        })->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
