<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Registra comandos de consola (si no usas auto-discovery).
     */
    protected $commands = [
        \App\Console\Commands\EnviarRecordatoriosPagos::class, // Recordatorios WA/Email (7 días antes, hoy y vencidos)
        // \App\Console\Commands\SendEventRemindersCommand::class, // <- ya NO lo usamos
        \App\Console\Commands\RunAgenda::class, // 👈 Recordatorios de eventos (usa Job SendEventoReminderJob)
        // \App\Console\Commands\ReiniciarAsistencias::class,
        // \App\Console\Commands\NotificarPagosProximos::class,
        // \App\Console\Commands\LimpiarModulos::class,
    ];

    /**
     * Programa las tareas recurrentes (Laravel Scheduler).
     */
    protected function schedule(Schedule $schedule): void
    {
        $tz = config('app.timezone', 'America/Mexico_City');

        // Tareas existentes
        $schedule->command('reiniciar:asistencias')
            ->twiceMonthly(1, 16)
            ->timezone($tz);

        $schedule->command('pagos:notificar-proximos')
            ->dailyAt('08:00')
            ->timezone($tz);

        $schedule->command('modulos:limpiar')
            ->hourly()
            ->timezone($tz);

        // Recordatorios automáticos de pagos (ya los tenías)
        $schedule->command('pagos:recordatorios')
            ->everyTenMinutes()
            ->withoutOverlapping(15)
            ->onOneServer()
            ->timezone($tz);
 // cada hora (puedes cambiar a dailyAt('09:00'))
  $schedule->command('inventory:check-low-stock')->hourly();
        // ⚠️ Los recordatorios de EVENTOS ya NO dependen del scheduler,
        // porque los vas a disparar desde cron-job.org con la URL:
        //   /cron/eventos/reminders?token=...
        // que internamente llama al comando agenda:run.
        //
        // Si algún día quieres que también corran desde el scheduler de Laravel,
        // podrías descomentar esto:
        //
        // $schedule->command('agenda:run', ['--limit' => 200, '--window' => 5])
        //     ->everyMinute()
        //     ->withoutOverlapping()
        //     ->onOneServer()
        //     ->timezone($tz);
    }

    /**
     * Carga comandos de consola.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    /**
     * (Opcional) Zona horaria global del scheduler.
     */
    // protected function scheduleTimezone(): \DateTimeZone|string|null
    // {
    //     return config('app.timezone', 'America/Mexico_City');
    // }
}
