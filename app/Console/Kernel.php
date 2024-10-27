<?php

namespace App\Console;

use App\Http\Services\PenalizeService;
use App\Jobs\CalculateActiveClients;
use App\Jobs\PenalizeNonattendance;
use App\Jobs\CheckClientPlansExpiration;
use App\Jobs\ClearAssistedAchievement;
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
        'App\Console\Commands\ValidarKangoosReservados',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(new CheckClientPlansExpiration())->dailyAt('09:00');
        $schedule->call(new CalculateActiveClients())->dailyAt('01:00');
        $schedule->call(new ClearAssistedAchievement())->sundays()->at('23:59:59');
        $schedule->command("validator:kangosReservados")->everyMinute();
        $schedule->call(function (){$this->penalizeNonattendance();})->dailyAt('23:00');
        //$schedule->command("validator:transaccionesPendientes")->cron("*/5 * * * *");
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

    private function penalizeNonattendance() : void
    {
        $penalizeService = app(PenalizeService::class);
        $penalizeJob = new PenalizeNonattendance($penalizeService);
        $penalizeJob();
    }
}
