<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use Carbon\Carbon;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendShiftReminders extends Command
{
    // Este es el nombre del comando que ejecutará Railway
    protected $signature = 'reminders:send';

    protected $description = 'Envía recordatorios a los servidores 24 y 6 horas antes de su turno';

    public function handle()
    {
        $this->info('Revisando turnos próximos...');

        // Buscamos turnos pendientes o confirmados a los que les falte algún recordatorio
        $schedules = Schedule::with(['user', 'ministry'])
            ->whereIn('status', ['pendiente', 'confirmado'])
            ->where(function ($query) {
                $query->where('reminder_24h_sent', false)
                      ->orWhere('reminder_6h_sent', false);
            })
            ->get();

        foreach ($schedules as $schedule) {
            // Unimos la fecha y hora del turno para que Carbon lo entienda
            $shiftDateTime = Carbon::parse($schedule->date . ' ' . $schedule->start_time);
            
            // Calculamos cuántos minutos faltan desde AHORA hasta el turno
            $minutesLeft = now()->diffInMinutes($shiftDateTime, false); // 'false' devuelve negativo si ya pasó

            // Si el turno ya pasó, lo ignoramos
            if ($minutesLeft <= 0) continue;

            // --- REVISIÓN 24 HORAS (1440 minutos) ---
            if (!$schedule->reminder_24h_sent && $minutesLeft <= 1440) {
                $this->sendPush($schedule->user, "Recordatorio: Turno en 24h", "Sirves mañana a las {$schedule->start_time} en {$schedule->task}. ¡Prepárate!");
                $schedule->reminder_24h_sent = true;
                $schedule->save();
                $this->info("Recordatorio de 24h enviado a {$schedule->user->name}");
            }

            // --- REVISIÓN 6 HORAS (360 minutos) ---
            if (!$schedule->reminder_6h_sent && $minutesLeft <= 360) {
                $this->sendPush($schedule->user, "Recordatorio: Turno en 6h", "Hoy tienes servicio a las {$schedule->start_time}. ¡No llegues tarde!");
                $schedule->reminder_6h_sent = true;
                $schedule->save();
                $this->info("Recordatorio de 6h enviado a {$schedule->user->name}");
            }
        }

        $this->info('Revisión completada.');
    }

    /**
     * Función privada para conectarse a Firebase y disparar el mensaje
     */
    private function sendPush($user, $title, $body)
    {
        if (!$user || !$user->fcm_token) return;

        try {
            $factory = (new Factory)->withServiceAccount(json_decode(env('FIREBASE_CREDENTIALS'), true));
            $messaging = $factory->createMessaging();

            $message = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification(Notification::create($title, $body));

            $messaging->send($message);
        } catch (\Exception $e) {
            \Log::error("Error enviando recordatorio a {$user->name}: " . $e->getMessage());
        }
    }
}