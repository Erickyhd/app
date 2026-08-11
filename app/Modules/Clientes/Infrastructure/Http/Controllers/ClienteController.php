<?php

namespace App\Modules\Clientes\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Clientes\Domain\Models\Cliente;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Exception;

class ClienteController extends Controller
{
    public function index()
    {
        try {
            $clientes = Cliente::all();
            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Clientes obtenidos exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los clientes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'nullable|email',
                'telefono' => 'nullable|string|max:50',
                'direccion' => 'nullable|string',
            ]);

            $data['usuario_creacion_id'] = Auth::id() ?? 1;

            $cliente = Cliente::create($data);

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente creado exitosamente.'
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
                'message' => 'Error interno al crear el cliente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente encontrado.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado o inactivo.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'email' => 'nullable|email',
                'telefono' => 'nullable|string|max:50',
                'direccion' => 'nullable|string',
            ]);

            $data['usuario_actualizacion_id'] = Auth::id() ?? 1;

            $cliente->update($data);

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente actualizado exitosamente.'
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
                'message' => 'Error al actualizar el cliente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            
            $cliente->update([
                'estado' => 0,
                'usuario_actualizacion_id' => Auth::id() ?? 1
            ]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Cliente eliminado lógicamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
