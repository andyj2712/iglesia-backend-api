<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MinistryController extends Controller
{
    /**
     * Listar todos los ministerios con el conteo de sus miembros.
     */
    public function index()
    {
        $ministries = Ministry::withCount('users')->get();
        return response()->json($ministries, 200);
    }

    /**
     * Crear un nuevo ministerio (Operación típica del Admin).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ministries,name',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ministry = Ministry::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Ministerio creado con éxito',
            'ministry' => $ministry
        ], 201);
    }

    /**
     * Mostrar un ministerio específico junto con los usuarios que pertenecen a él.
     */
    public function show($id)
    {
        $ministry = Ministry::with('users.role')->find($id);

        if (!$ministry) {
            return response()->json(['message' => 'Ministerio no encontrado'], 404);
        }

        return response()->json($ministry, 200);
    }

    /**
     * Actualizar los datos de un ministerio.
     */
    public function update(Request $request, $id)
    {
        $ministry = Ministry::find($id);

        if (!$ministry) {
            return response()->json(['message' => 'Ministerio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:ministries,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ministry->update($request->all());

        return response()->json([
            'message' => 'Ministerio actualizado con éxito',
            'ministry' => $ministry
        ], 200);
    }

    /**
     * Eliminar un ministerio.
     */
    public function destroy($id)
    {
        $ministry = Ministry::find($id);

        if (!$ministry) {
            return response()->json(['message' => 'Ministerio no encontrado'], 404);
        }

        $ministry->delete();

        return response()->json(['message' => 'Ministerio eliminado correctamente'], 200);
    }

    /**
     * Asignar un usuario (Servidor/Líder) a un ministerio específico.
     */
    public function assignUser(Request $request, $id)
    {
        $ministry = Ministry::find($id);

        if (!$ministry) {
            return response()->json(['message' => 'Ministerio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // syncWithoutDetaching añade el registro a la tabla pivot solo si no existe previamente
        $ministry->users()->syncWithoutDetaching([$request->user_id]);

        return response()->json([
            'message' => 'Usuario asignado al ministerio correctamente'
        ], 200);
    }

    /**
     * Remover a un usuario de un ministerio.
     */
    public function removeUser(Request $request, $id)
    {
        $ministry = Ministry::find($id);

        if (!$ministry) {
            return response()->json(['message' => 'Ministerio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Rompe la relación en la tabla pivot
        $ministry->users()->detach($request->user_id);

        return response()->json([
            'message' => 'Usuario removido del ministerio correctamente'
        ], 200);
    }
}