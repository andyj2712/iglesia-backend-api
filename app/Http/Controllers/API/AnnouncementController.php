<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class AnnouncementController extends Controller
{
    /**
     * Listar los anuncios del muro.
     * Si se envía ?ministry_id=X, mostrará los anuncios de ese ministerio + los generales.
     */
    public function index(Request $request)
    {
        // Cargamos al usuario actual y la relación con sus ministerios
        $user = $request->user()->load(['ministries', 'role']);
        
        $query = Announcement::with(['user:id,name', 'ministry:id,name']);

        // Si NO es Administrador, filtramos los anuncios
        if ($user->role->name !== 'Admin') {
            // Obtenemos un arreglo solo con los IDs de los ministerios a los que pertenece
            $userMinistryIds = $user->ministries->pluck('id');
            
            $query->where(function($q) use ($userMinistryIds) {
                // Mostrar los anuncios que coincidan con sus ministerios...
                $q->whereIn('ministry_id', $userMinistryIds)
                  // ... O los que sean generales (ministry_id = null)
                  ->orWhereNull('ministry_id'); 
            });
        }

        $announcements = $query->orderBy('created_at', 'desc')->get();

        return response()->json($announcements, 200);
    }

    /**
     * Crear una nueva publicación (Anuncio, versículo o reflexión).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ministry_id' => 'nullable|exists:ministries,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $imagePath = Storage::url($path);
        }

        $announcement = Announcement::create([
            'ministry_id' => $request->ministry_id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath
        ]);

        // ─── LÓGICA DE NOTIFICACIONES PUSH (FIREBASE) ───
        try {
            // 1. Buscar a quién enviarle el mensaje (usuarios con token fcm que no sean el creador)
            $query = User::whereNotNull('fcm_token')->where('id', '!=', $request->user()->id);
            
            if ($request->ministry_id) {
                // Si es de un ministerio específico, filtramos
                $query->whereHas('ministries', function($q) use ($request) {
                    $q->where('ministries.id', $request->ministry_id);
                });
            }
            
            $tokens = $query->pluck('fcm_token')->toArray();

            // 2. Si hay teléfonos registrados, disparamos el mensaje
            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('firebase-credentials.json'));
                $messaging = $factory->createMessaging();

                $message = CloudMessage::new()->withNotification(
                    Notification::create('Nuevo Anuncio', $request->title)
                );

                // sendMulticast envía a un arreglo de múltiples tokens de un solo golpe
                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Exception $e) {
            // Silenciamos el error para que si Firebase falla, el anuncio de todos modos se guarde
            \Log::error('Error enviando notificación de anuncio: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Publicación creada con éxito',
            'announcement' => $announcement->load(['user:id,name', 'ministry:id,name'])
        ], 201);
    }
    /**
     * Actualizar una publicación existente.
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Publicación no encontrada'], 404);
        }

        // Validamos los datos que envía el frontend
        $validator = Validator::make($request->all(), [
            'ministry_id' => 'nullable|exists:ministries,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Actualizamos la base de datos
        $announcement->update([
            'ministry_id' => $request->ministry_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Publicación actualizada con éxito',
            'announcement' => $announcement->load(['user:id,name', 'ministry:id,name'])
        ], 200);
    }

    /**
     * Mostrar una publicación específica.
     */
    public function show($id)
    {
        $announcement = Announcement::with(['user:id,name', 'ministry:id,name'])->find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Publicación no encontrada'], 404);
        }

        return response()->json($announcement, 200);
    }

    /**
     * Eliminar un anuncio (y su imagen asociada del disco si existe).
     */
    public function destroy($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Publicación no encontrada'], 404);
        }

        // Borrar la imagen física del servidor para no acumular basura
        if ($announcement->image_path) {
            // Extraer el nombre relativo del archivo
            $relativeStoragePath = str_replace('/storage/', '', $announcement->image_path);
            Storage::disk('public')->delete($relativeStoragePath);
        }

        $announcement->delete();

        return response()->json(['message' => 'Publicación eliminada correctamente'], 200);
    }
}