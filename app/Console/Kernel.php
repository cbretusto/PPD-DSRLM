<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // DAILY EMAIL
        $schedule->command('email:percentage')
            ->dailyAt('06:00')
            ->timezone('Asia/Manila')
            ->withoutOverlapping(60)
            ->onOneServer();

        // MONTHLY EMAIL
        $schedule->command('email:monthly-report')
            ->monthlyOn(1, '06:00')
            ->timezone('Asia/Manila')
            ->withoutOverlapping(60)
            ->onOneServer();
            //  ->everyMinute()
            // ->withoutOverlapping(60);

        // OLD DAILY EMAIL
        // $schedule->command('email:percentage')
        //     ->dailyAt('06:00')
        //     ->timezone('Asia/Manila')
        //     ->withoutOverlapping(60)
        //     ->onOneServer();
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
