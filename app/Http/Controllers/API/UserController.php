<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Listar todos los usuarios con su respectivo rol.
     */
    public function index()
    {
        // Traemos a los usuarios ordenados alfabéticamente
        $users = User::with('role', 'ministries')->orderBy('name', 'asc')->get();
        return response()->json($users, 200);
    }

    /**
     * Crear un nuevo usuario (Líder o Servidor).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'ministry_ids' => 'nullable|array',
            'ministry_ids.*' => 'exists:ministries,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptamos la contraseña
            'role_id' => $request->role_id,
        ]);
        
        return response()->json([
            'message' => 'Usuario creado con éxito',
            'user' => $user->load('role')
        ], 201);

        // Sincronizar los ministerios en la tabla pivot
        if ($request->has('ministry_ids')) {
            $user->ministries()->sync($request->ministry_ids);
        }

        return response()->json([
            'message' => 'Usuario creado con éxito',
            'user' => $user->load(['role', 'ministries'])
        ], 201);
    }

    /**
     * Ver el detalle de un usuario específico.
     */
    public function show($id)
    {
        $user = User::with('role', 'ministries')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    /**
     * Actualizar los datos de un usuario (Nombre, Correo, Rol y Contraseña si se solicita).
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            // El correo debe ser único, excepto si es el mismo del usuario actual
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6', // Nullable: Solo si quieren cambiarla
            'role_id' => 'sometimes|required|exists:roles,id',
            'ministry_ids' => 'nullable|array', // <-- Nueva validación
            'ministry_ids.*' => 'exists:ministries,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Preparamos los datos a actualizar
        $dataToUpdate = $request->except(['password', 'ministry_ids']); // Excluimos la contraseña temporalmente

        // Si el administrador escribió una nueva contraseña, la encriptamos y la añadimos
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);
        // Sincronizar (Laravel añade los nuevos, mantiene los existentes y borra los que se hayan quitado)
        if ($request->has('ministry_ids')) {
            $user->ministries()->sync($request->ministry_ids);
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user->load(['role', 'ministries'])
        ], 200);
    }

    /**
     * Eliminar un usuario del sistema (Esto también borrará sus asignaciones en cascada por la base de datos).
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Regla de negocio vital: Evitar que el administrador se borre a sí mismo por error
        if ($user->id === request()->user()->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta administrativa.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
    /**
     * Guardar o actualizar el Token de Firebase del dispositivo del usuario.
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'Token de dispositivo actualizado correctamente.']);
    }
}