<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modulesWithActions = [
            'marcas' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'categorias' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'clientes' => ['crear', 'leer', 'actualizar', 'eliminar', 'reportes'],
            'formapagos' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'cajas' => ['crear', 'leer', 'actualizar', 'cerrar', 'eliminar', 'reportes'],
            'proveedores' => ['crear', 'leer', 'actualizar', 'eliminar', 'reportes'],
            'productos' => ['crear', 'leer', 'actualizar', 'eliminar', 'reportes'],
            'sucursales' => ['crear', 'leer', 'actualizar', 'eliminar', 'cambiar'],
            'usuarios' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'roles' => ['crear', 'leer', 'actualizar', 'eliminar', 'asignar'],
            'ventas' => ['crear', 'leer', 'anular', 'reportes'],
            'ventas a credito' => ['leer', 'pagos'],
            'cotizaciones' => ['crear', 'leer', 'eliminar', 'reportes'],
            'reparaciones' => ['crear', 'leer', 'actualizar', 'eliminar', 'repuestos', 'reportes'],
            'compras' => ['crear', 'leer', 'anular', 'reportes'],
        ];

        foreach ($modulesWithActions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::create(['name' => "$action $module"]);
            }
        }
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
