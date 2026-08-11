<?php

namespace App\Modules\Rangos\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Rangos\Domain\Models\Rango;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Exception;

class RangoController extends Controller
{
    public function index()
    {
        try {
            $rangos = Rango::all();
            return response()->json([
                'success' => true,
                'data' => $rangos,
                'message' => 'Rangos obtenidos exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los rangos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'nivel' => 'required|integer',
                'descripcion' => 'nullable|string',
            ]);

            $data['usuario_creacion_id'] = Auth::id() ?? 1;

            $rango = Rango::create($data);

            return response()->json([
                'success' => true,
                'data' => $rango,
                'message' => 'Rango creado exitosamente.'
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
                'message' => 'Error interno al crear el rango.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $rango = Rango::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $rango,
                'message' => 'Rango encontrado.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rango no encontrado o inactivo.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rango = Rango::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'nivel' => 'sometimes|required|integer',
                'descripcion' => 'nullable|string',
            ]);

            $data['usuario_actualizacion_id'] = Auth::id() ?? 1;

            $rango->update($data);

            return response()->json([
                'success' => true,
                'data' => $rango,
                'message' => 'Rango actualizado exitosamente.'
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
                'message' => 'Error al actualizar el rango.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $rango = Rango::findOrFail($id);
            
            $rango->update([
                'estado' => 0,
                'usuario_actualizacion_id' => Auth::id() ?? 1
            ]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Rango eliminado lógicamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rango.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
