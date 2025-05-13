<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolController extends Controller
{
    // Mostrar todos los roles
    public function index()
    {
        $roles = Role::all();
        return view('admin.usuarios.rol', compact('roles'));
    }

    // Formulario para crear un nuevo rol
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.usuarios.createRol', compact('permissions'));
    }

    // Asignar roles y permisos a un usuario
    public function assignRoles($encryptedId)
    {
        $userId = decrypt($encryptedId);
        $user = User::findOrFail($userId);
        $roles = Role::all();
        $permissions = Permission::all();

        // Obtener los roles y permisos actuales del usuario
        $userPermissions = $user->permissions->pluck('name')->toArray();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.usuarios.updateRole', compact('user', 'roles', 'permissions', 'userPermissions', 'userRoles'));
    }

    // Guardar un nuevo rol
    public function store(Request $request)
    {
        // Validación de datos
        $validated = $request->validate([
            'role_name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        // Crear el nuevo rol
        $role = Role::create(['name' => $validated['role_name']]);

        // Asignar permisos al rol
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('roles.index')->with('success', 'Rol creado y permisos asignados correctamente.');
    }

    // Mostrar formulario para editar un rol
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('admin.usuarios.editRol', compact('role', 'permissions'));
    }

    // Actualizar un rol
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validated = $request->validate([
            'role_name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($id);

        // Actualizar el nombre del rol
        $role->update(['name' => $validated['role_name']]);

        // Sincronizar los permisos
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('roles.index')->with('success', 'Rol y permisos actualizados correctamente.');
    }

    // Eliminar un rol
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Verificar que el rol no esté siendo utilizado por algún usuario
        if ($role->users->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Este rol no se puede eliminar porque está asignado a uno o más usuarios.');
        }

        // Eliminar el rol
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    // Actualizar roles y permisos de un usuario
    public function updateRoles(Request $request, $encryptedId)
    {
        $userId = decrypt($encryptedId);
        $user = User::findOrFail($userId);

        // Actualizar roles
        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));  // Sincroniza los roles seleccionados
        }

        return redirect()->route('users.index')->with('message', 'Roles y permisos actualizados correctamente.');
    }
}
