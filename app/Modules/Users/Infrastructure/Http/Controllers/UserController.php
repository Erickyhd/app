<?php

namespace App\Modules\Users\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Users\Domain\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

use Illuminate\Support\Facades\DB;
use App\Modules\Trabajadores\Domain\Models\Trabajador;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('trabajador')->where('estado', 1)->orderBy('created_at', 'desc')->get();
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
                'dni' => 'nullable|string|max:20|unique:trabajadores,numero_documento',
                'nombres' => 'required|string|max:255',
                'apellidos' => 'nullable|string|max:255',
                'email' => 'required|string|email|max:255|unique:usuarios,email',
                'password' => 'required|string|min:6',
                'telefono' => 'nullable|string|max:20',
                'genero' => 'nullable|string|max:20',
                'jerarquia_id' => 'nullable|exists:jerarquias,id',
                'rango_id' => 'nullable|exists:rangos,id',
            ]);

            DB::beginTransaction();

            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'jerarquia_id' => $validated['jerarquia_id'] ?? null,
                'rango_id' => $validated['rango_id'] ?? null,
                'usuario_creacion_id' => Auth::id() ?? 1,
            ]);

            Trabajador::create([
                'usuario_id' => $user->id,
                'tipo_documento' => 'DNI',
                'numero_documento' => $validated['dni'] ?? 'S/N-' . uniqid(),
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'] ?? '',
                'telefono_principal' => $validated['telefono'] ?? '',
                'genero' => $validated['genero'] ?? null,
                'fecha_contratacion' => now()->toDateString(),
                'usuario_creacion_id' => Auth::id() ?? 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $user->load('trabajador'),
                'message' => 'Usuario creado exitosamente.'
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
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
            $user = User::with('trabajador')->where('estado', 1)->findOrFail($id);
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
            $user = User::with('trabajador')->findOrFail($id);
            $trabajador = $user->trabajador;

            $validated = $request->validate([
                'dni' => 'nullable|string|max:20|unique:trabajadores,numero_documento,' . ($trabajador ? $trabajador->id : ''),
                'nombres' => 'sometimes|required|string|max:255',
                'apellidos' => 'nullable|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:usuarios,email,' . $user->id,
                'password' => 'nullable|string|min:6',
                'telefono' => 'nullable|string|max:20',
                'genero' => 'nullable|string|max:20',
                'jerarquia_id' => 'nullable|exists:jerarquias,id',
                'rango_id' => 'nullable|exists:rangos,id',
            ]);

            DB::beginTransaction();

            if (isset($validated['email'])) $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            if (array_key_exists('jerarquia_id', $validated)) $user->jerarquia_id = $validated['jerarquia_id'];
            if (array_key_exists('rango_id', $validated)) $user->rango_id = $validated['rango_id'];
            
            $user->usuario_actualizacion_id = Auth::id() ?? 1;
            $user->save();

            if ($trabajador) {
                if (array_key_exists('dni', $validated)) $trabajador->numero_documento = $validated['dni'] ?? $trabajador->numero_documento;
                if (isset($validated['nombres'])) $trabajador->nombres = $validated['nombres'];
                if (array_key_exists('apellidos', $validated)) $trabajador->apellidos = $validated['apellidos'] ?? '';
                if (array_key_exists('telefono', $validated)) $trabajador->telefono_principal = $validated['telefono'] ?? '';
                if (array_key_exists('genero', $validated)) $trabajador->genero = $validated['genero'];
                
                $trabajador->usuario_actualizacion_id = Auth::id() ?? 1;
                $trabajador->save();
            } else {
                Trabajador::create([
                    'usuario_id' => $user->id,
                    'tipo_documento' => 'DNI',
                    'numero_documento' => $validated['dni'] ?? 'S/N-' . uniqid(),
                    'nombres' => $validated['nombres'] ?? 'Usuario',
                    'apellidos' => $validated['apellidos'] ?? '',
                    'telefono_principal' => $validated['telefono'] ?? '',
                    'genero' => $validated['genero'] ?? null,
                    'fecha_contratacion' => now()->toDateString(),
                    'usuario_creacion_id' => Auth::id() ?? 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $user->load('trabajador'),
                'message' => 'Usuario actualizado exitosamente.'
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
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
            
            if (Auth::id() === $user->id) {
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
