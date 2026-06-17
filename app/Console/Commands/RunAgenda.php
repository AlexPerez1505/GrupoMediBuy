<?php

namespace App\Console\Commands;

use App\Jobs\SendEventoReminderJob;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunAgenda extends Command
{
    /**
     * Ejecutas:
     *   php artisan agenda:run --limit=200 --window=5
     *
     *  - limit  = máximo de eventos a procesar
     *  - window = ventana (en minutos) hacia atrás para considerar recordatorios.
     */
    protected $signature = 'agenda:run {--limit=200} {--window=5}';

    protected $description = 'Envía recordatorios de eventos (correo + WhatsApp mediante plantilla)';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $window = max(1, (int) $this->option('window')); // mínimo 1 minuto

        $tz   = config('app.timezone', 'America/Mexico_City');
        $now  = Carbon::now($tz);
        $from = $now->copy()->subMinutes($window);

        Log::info("agenda:run → ventana {$window} min", [
            'from' => $from->toDateTimeString(),
            'to'   => $now->toDateTimeString(),
        ]);
        $this->info("Buscando eventos con next_reminder_at entre {$from} y {$now}");

        $eventos = Evento::query()
            ->whereNotNull('next_reminder_at')
            ->whereBetween('next_reminder_at', [$from, $now])
            ->orderBy('next_reminder_at')
            ->limit($limit)
            ->get();

        $count = $eventos->count();

        Log::info("agenda:run → eventos encontrados", ['count' => $count]);
        $this->info("Eventos a notificar: {$count}");

        foreach ($eventos as $evento) {
            $this->info("→ Evento {$evento->id}: {$evento->title}");

            try {
                // Ejecutamos el Job de forma síncrona (sin worker de colas)
                SendEventoReminderJob::dispatchSync($evento->id);
            } catch (\Throwable $e) {
                Log::error('agenda:run → error ejecutando SendEventoReminderJob', [
                    'evento_id' => $evento->id,
                    'error'     => $e->getMessage(),
                ]);
                $this->error("Error al enviar recordatorio para ID {$evento->id}: {$e->getMessage()}");
            }
        }

        $this->info('agenda:run terminado');
        Log::info('agenda:run → terminado');

        return self::SUCCESS;
    }
}
