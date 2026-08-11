<?php

namespace App\Modules\Users\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Users\Domain\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::where('estado', 1)->orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Usuarios obtenidos exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los usuarios.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:usuarios,email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'nombres' => $validated['nombres'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'usuario_creacion_id' => Auth::id() ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario creado exitosamente.'
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
                'message' => 'Error interno al crear el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::where('estado', 1)->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario encontrado.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado o inactivo.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'nombres' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:usuarios,email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]);

            if (isset($validated['nombres'])) $user->nombres = $validated['nombres'];
            if (isset($validated['email'])) $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->usuario_actualizacion_id = Auth::id() ?? 1;

            $user->save();

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario actualizado exitosamente.'
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
                'message' => 'Error al actualizar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if (auth()->id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta.'
                ], 403);
            }

            $user->estado = 0;
            $user->usuario_actualizacion_id = Auth::id() ?? 1;
            $user->save();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Usuario eliminado lógicamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
