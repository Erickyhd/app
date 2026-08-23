import { useAuthStore } from '../../../store/authStore';

export function usePermissions() {
    const authStore = useAuthStore();

    /**
     * Verifica si el usuario tiene un permiso específico
     * @param {String} permissionName Nombre del permiso (ej. 'usuarios.ver')
     * @returns {Boolean}
     */
    const can = (permissionName) => {
        if (!authStore.permissions) return false;
        return authStore.permissions.includes(permissionName);
    };

    /**
     * Verifica si el usuario tiene al menos un permiso de un grupo
     * @param {Array<String>} permissions Lista de permisos
     * @returns {Boolean}
     */
    const canAny = (permissions) => {
        if (!authStore.permissions) return false;
        return permissions.some(p => authStore.permissions.includes(p));
    };

    /**
     * Verifica si el usuario tiene un rol específico
     * @param {String} roleName Nombre del rol
     * @returns {Boolean}
     */
    const hasRole = (roleName) => {
        if (!authStore.roles) return false;
        return authStore.roles.includes(roleName);
    };

    return {
        can,
        canAny,
        hasRole
    };
}
