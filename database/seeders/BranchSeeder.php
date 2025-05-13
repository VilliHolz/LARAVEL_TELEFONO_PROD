<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Sucursal 1',
            'phone' => '123456789',
            'address' => 'Calle Ficticia 123',
            'email' => 'administrador@gmail.com',
            'representative' => 'Angel Sifuentes',
            'status' => 'Activo',
        ]);

        Branch::create([
            'name' => 'Sucursal 2',
            'phone' => '987654321',
            'address' => 'Avenida Real 456',
            'email' => 'sucursal2@example.com',
            'representative' => 'Ana Gomez',
            'status' => 'Activo',
        ]);
    }
}
