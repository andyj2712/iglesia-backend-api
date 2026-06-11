<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class ScheduleController extends Controller
{
    /**
     * Listar todos los turnos. 
     * Se puede filtrar por ministerio enviando ?ministry_id=1 en la URL
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['user', 'ministry']);

        // Si el líder quiere ver solo los turnos de su ministerio
        if ($request->has('ministry_id')) {
            $query->where('ministry_id', $request->ministry_id);
        }

        // Ordenamos para que los turnos más próximos salgan primero
        $schedules = $query->orderBy('date', 'asc')->orderBy('start_time', 'asc')->get();

        return response()->json($schedules, 200);
    }

    /**
     * Asignar a un servidor a una fecha, hora y tarea específica.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ministry_id' => 'required|exists:ministries,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'task' => 'required|string|max:255',
            'status' => 'nullable|in:pendiente,confirmado,cancelado'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = Schedule::create([
            'ministry_id' => $request->ministry_id,
            'user_id' => $request->user_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'task' => $request->task,
            'status' => $request->status ?? 'pendiente'
        ]);

        // ─── LÓGICA DE NOTIFICACIONES PUSH (FIREBASE) ───
        try {
            $assignedUser = User::find($request->user_id);

            // Si el usuario asignado tiene su celular registrado (fcm_token)
            if ($assignedUser && $assignedUser->fcm_token) {
                $factory = (new Factory)->withServiceAccount(json_decode(env('FIREBASE_CREDENTIALS'), true));
                $messaging = $factory->createMessaging();

                // Formateamos la fecha visualmente para el mensaje
                $fechaVisual = date('d/m/Y', strtotime($request->date));

                $message = CloudMessage::withTarget('token', $assignedUser->fcm_token)
                    ->withNotification(Notification::create(
                        'Nuevo Servicio Asignado', 
                        "Tienes un turno el {$fechaVisual} a las {$request->start_time} ({$request->task})"
                    ));

                $messaging->send($message);
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de turno: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Turno asignado correctamente',
            'schedule' => $schedule->load(['user', 'ministry']) 
        ], 201);
    }

    /**
     * Obtener el calendario personal del servidor que hizo login.
     */
    public function mySchedules(Request $request)
    {
        $user = $request->user();
        $today = Carbon::now()->toDateString(); // Obtiene la fecha de hoy "YYYY-MM-DD"

        $schedules = Schedule::with('ministry')
            ->where('user_id', $user->id)
            ->where('date', '>=', $today) // Solo muestra turnos de hoy hacia el futuro
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json($schedules, 200);
    }

    /**
     * Actualizar una programación.
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Turno no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ministry_id' => 'sometimes|required|exists:ministries,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'date' => 'sometimes|required|date',
            // Relajamos un poco la regla de la hora para evitar choques con los segundos de MySQL
            'start_time' => 'sometimes|required', 
            'task' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:pendiente,confirmado,cancelado'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule->update($request->all());

        return response()->json([
            'message' => 'Turno actualizado con éxito',
            // Cargamos la relación para que el calendario frontend se actualice sin perder nombres
            'schedule' => $schedule->load(['user', 'ministry'])
        ], 200);
    }

    /**
     * Eliminar un turno del calendario.
     */
    public function destroy($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Turno no encontrado'], 404);
        }

        $schedule->delete();

        return response()->json(['message' => 'Turno eliminado correctamente'], 200);
    }
}