<?php

namespace App\Modules\Users\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Users\Domain\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Exception;

class RolePermissionController extends Controller
{
    /**
     * Obtener todos los roles disponibles
     */
    public function getRoles()
    {
        try {
            $roles = Role::all();
            return response()->json([
                'success' => true,
                'data' => $roles
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los permisos agrupados por módulo
     */
    public function getPermissions()
    {
        try {
            $permissions = Permission::all();
            $grouped = [];

            foreach ($permissions as $permission) {
                // Asumiendo formato "modulo.accion" o "modulo.submodulo.accion"
                $parts = explode('.', $permission->name);
                $module = $parts[0];
                
                if (!isset($grouped[$module])) {
                    $grouped[$module] = [];
                }
                
                $grouped[$module][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => end($parts)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $grouped
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener roles y permisos de un usuario específico
     */
    public function getUserPermissions($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Permisos directos del usuario (no los heredados por rol)
            $directPermissions = $user->getDirectPermissions()->pluck('name');
            
            // Roles del usuario
            $roles = $user->roles->pluck('name');
            
            // Todos los permisos (directos + heredados por rol)
            $allPermissions = $user->getAllPermissions()->pluck('name');

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->only(['id', 'nombres', 'apellidos', 'email']),
                    'roles' => $roles,
                    'direct_permissions' => $directPermissions,
                    'all_permissions' => $allPermissions
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizar roles y permisos directos para un usuario
     */
    public function syncUserPermissions(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'roles' => 'array',
                'roles.*' => 'string|exists:roles,name',
                'permissions' => 'array',
                'permissions.*' => 'string|exists:permissions,name'
            ]);

            // Sincronizar roles (reemplaza los existentes)
            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            // Sincronizar permisos directos (reemplaza los existentes)
            if (isset($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Roles y permisos actualizados exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar roles y permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
