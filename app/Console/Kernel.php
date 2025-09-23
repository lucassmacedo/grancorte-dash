<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('import:clientes')->everyTenMinutes()->between('7:00', '21:00');
        $schedule->command('import:vendedores')->everyThirtyMinutes()->between('7:00', '21:00');
        $schedule->command('import:dashboard')->everyThirtyMinutes()->between('7:00', '21:00');
        $schedule->command('import:produtos')->everyThirtyMinutes()->between('7:00', '21:00');
//        $schedule->command('import:filiais')->dailyAt('00:00');
        //        $schedule->command('finish-prospect')->everyThirtyMinutes()->between('7:00', '21:00');
        $schedule->command('import:logistica')->everyThirtyMinutes()->between('21:00', '10:00');
        $schedule->command('import:notas')->everyThirtyMinutes()
            ->after(function () {
                Artisan::call('import:pedidos-faturados');
                Artisan::call('import:titulos');
                Artisan::call('import:logistica');
                Artisan::call('clientes:generate-score');
            });

        $schedule->command('import:pendencias-financeiras')->everyThirtyMinutes()->between('7:00', '21:00');
        $schedule->command('update:latlong')->everyThirtyMinutes()->between('7:00', '21:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}