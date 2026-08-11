<?php

namespace App\Modules\Jerarquias\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Jerarquias\Domain\Models\Jerarquia;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Exception;

class JerarquiaController extends Controller
{
    public function index()
    {
        try {
            $jerarquias = Jerarquia::with('parent')->get();
            return response()->json([
                'success' => true,
                'data' => $jerarquias,
                'message' => 'Jerarquías obtenidas exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las jerarquías.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'jerarquia_padre_id' => 'nullable|exists:jerarquias,id',
                'codigo' => 'nullable|string|max:50',
            ]);

            $data['usuario_creacion_id'] = Auth::id() ?? 1; // Fallback to 1 for testing if not logged in

            $jerarquia = Jerarquia::create($data);

            return response()->json([
                'success' => true,
                'data' => $jerarquia,
                'message' => 'Jerarquía creada exitosamente.'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno al crear la jerarquía.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $jerarquia = Jerarquia::with('parent')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $jerarquia,
                'message' => 'Jerarquía encontrada.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jerarquía no encontrada o inactiva.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $jerarquia = Jerarquia::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'jerarquia_padre_id' => 'nullable|exists:jerarquias,id',
                'codigo' => 'nullable|string|max:50',
            ]);

            $data['usuario_actualizacion_id'] = Auth::id() ?? 1;

            $jerarquia->update($data);

            return response()->json([
                'success' => true,
                'data' => $jerarquia,
                'message' => 'Jerarquía actualizada exitosamente.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la jerarquía.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $jerarquia = Jerarquia::findOrFail($id);
            
            $jerarquia->update([
                'estado' => 0,
                'usuario_actualizacion_id' => Auth::id() ?? 1
            ]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Jerarquía eliminada lógicamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la jerarquía.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
