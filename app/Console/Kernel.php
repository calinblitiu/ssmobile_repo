<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FetchLiveSoccer as FetchLiveSoccerCommand;
use App\Console\Commands\FetchLiveSoccerNews as FetchLiveSoccerNewsCommand;
use App\Console\Commands\FetchLiveSoccerEventScore as FetchLiveSoccerEventScoreCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        FetchLiveSoccerCommand::class,
        FetchLiveSoccerNewsCommand::class,
        Commands\FetchLiveSoccerEventScore::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         
        $schedule->command('fetch:livesoccer')->everyMinute();
        $schedule->command('fetch:livesoccernews')->hourly();
        $schedule->command('fetch:livesoccereventscore')->everyMinute();
                 // ->cron('*/2 * * * *');
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
