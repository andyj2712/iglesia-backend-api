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
     */
    public function index(Request $request)
    {
        $user = $request->user()->load(['ministries', 'role']);
        $query = Announcement::with(['user:id,name', 'ministry:id,name']);

        if ($user->role->name !== 'Admin') {
            $userMinistryIds = $user->ministries->pluck('id');
            $query->where(function($q) use ($userMinistryIds) {
                $q->whereIn('ministry_id', $userMinistryIds)
                  ->orWhereNull('ministry_id'); 
            });
        }

        $announcements = $query->orderBy('created_at', 'desc')->get();
        return response()->json($announcements, 200);
    }

    /**
     * Crear una nueva publicación (Guardando la imagen en el servidor).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ministry_id' => 'nullable|exists:ministries,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        
        // ─── LÓGICA DE ALMACENAMIENTO LOCAL ───
        if ($request->hasFile('image')) {
            // Guarda la imagen en la carpeta storage/app/public/announcements
            $path = $request->file('image')->store('announcements', 'public');
            // Genera la URL pública (Ej: /storage/announcements/xxx.jpg)
            $imagePath = Storage::url($path); 
        }

        $announcement = Announcement::create([
            'ministry_id' => $request->ministry_id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath
        ]);

        // ─── NOTIFICACIONES FIREBASE ───
        try {
            $query = User::whereNotNull('fcm_token')->where('id', '!=', $request->user()->id);
            if ($request->ministry_id) {
                $query->whereHas('ministries', function($q) use ($request) {
                    $q->where('ministries.id', $request->ministry_id);
                });
            }
            $tokens = $query->pluck('fcm_token')->toArray();

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(json_decode(env('FIREBASE_CREDENTIALS'), true));
                $messaging = $factory->createMessaging();
                $message = CloudMessage::new()->withNotification(
                    Notification::create('Nuevo Anuncio', $request->title)
                );
                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Publicación creada con éxito',
            'announcement' => $announcement->load(['user:id,name', 'ministry:id,name'])
        ], 201);
    }

    /**
     * Actualizar una publicación.
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Publicación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ministry_id' => 'nullable|exists:ministries,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Si mandan una nueva imagen, borramos la vieja y guardamos la nueva
        if ($request->hasFile('image')) {
            if ($announcement->image_path) {
                $relativeStoragePath = str_replace('/storage/', '', $announcement->image_path);
                Storage::disk('public')->delete($relativeStoragePath);
            }
            $path = $request->file('image')->store('announcements', 'public');
            $announcement->image_path = Storage::url($path);
        }

        $announcement->ministry_id = $request->ministry_id;
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->save();

        return response()->json([
            'message' => 'Publicación actualizada con éxito',
            'announcement' => $announcement->load(['user:id,name', 'ministry:id,name'])
        ], 200);
    }

    public function show($id)
    {
        $announcement = Announcement::with(['user:id,name', 'ministry:id,name'])->find($id);
        if (!$announcement) return response()->json(['message' => 'No encontrada'], 404);
        return response()->json($announcement, 200);
    }

    /**
     * Eliminar un anuncio (y borrar su foto local).
     */
    public function destroy($id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) return response()->json(['message' => 'Publicación no encontrada'], 404);
        
        // Borrar imagen física del servidor de Railway
        if ($announcement->image_path) {
            $relativeStoragePath = str_replace('/storage/', '', $announcement->image_path);
            Storage::disk('public')->delete($relativeStoragePath);
        }

        $announcement->delete();

        return response()->json(['message' => 'Publicación eliminada correctamente'], 200);
    }
}