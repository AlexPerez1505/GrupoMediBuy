<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        // Registra aquí tus comandos personalizados
        \App\Console\Commands\NotificarPagosProximos::class,
        \App\Console\Commands\ReiniciarAsistencias::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejemplo: comando para reiniciar asistencias dos veces al mes
        $schedule->command('reiniciar:asistencias')->twiceMonthly(1, 16);

        // Comando para notificar pagos próximos cada día a las 8:00 AM
        $schedule->command('notificar:pagos-proximos')->dailyAt('08:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
