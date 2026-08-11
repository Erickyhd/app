<?php

namespace App\Modules\Trabajadores\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Trabajadores\Domain\Models\Trabajador;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Exception;

class TrabajadorController extends Controller
{
    public function index()
    {
        try {
            $trabajadores = Trabajador::with('user')->get();
            return response()->json([
                'success' => true,
                'data' => $trabajadores,
                'message' => 'Trabajadores obtenidos exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajadores.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'nullable|exists:usuarios,id|unique:trabajadores,user_id',
                'tipo_documento' => 'required|string',
                'numero_documento' => 'required|string|unique:trabajadores,numero_documento',
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'fecha_nacimiento' => 'nullable|date',
                'genero' => 'nullable|string',
                'telefono_principal' => 'required|string|max:50',
                'direccion' => 'nullable|string',
                'fecha_contratacion' => 'required|date',
            ]);

            $data['usuario_creacion_id'] = Auth::id() ?? 1;

            $trabajador = Trabajador::create($data);

            return response()->json([
                'success' => true,
                'data' => $trabajador,
                'message' => 'Trabajador creado exitosamente.'
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
                'message' => 'Error interno al crear el trabajador.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $trabajador = Trabajador::with('user')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $trabajador,
                'message' => 'Trabajador encontrado.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trabajador no encontrado o inactivo.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $trabajador = Trabajador::findOrFail($id);

            $data = $request->validate([
                'user_id' => 'nullable|exists:usuarios,id|unique:trabajadores,user_id,'.$id,
                'tipo_documento' => 'sometimes|required|string',
                'numero_documento' => 'sometimes|required|string|unique:trabajadores,numero_documento,'.$id,
                'nombres' => 'sometimes|required|string|max:255',
                'apellidos' => 'sometimes|required|string|max:255',
                'fecha_nacimiento' => 'nullable|date',
                'genero' => 'nullable|string',
                'telefono_principal' => 'sometimes|required|string|max:50',
                'direccion' => 'nullable|string',
                'fecha_contratacion' => 'sometimes|required|date',
            ]);

            $data['usuario_actualizacion_id'] = Auth::id() ?? 1;

            $trabajador->update($data);

            return response()->json([
                'success' => true,
                'data' => $trabajador,
                'message' => 'Trabajador actualizado exitosamente.'
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
                'message' => 'Error al actualizar el trabajador.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $trabajador = Trabajador::findOrFail($id);
            
            $trabajador->update([
                'estado' => 0,
                'usuario_actualizacion_id' => Auth::id() ?? 1
            ]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Trabajador eliminado lógicamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el trabajador.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
