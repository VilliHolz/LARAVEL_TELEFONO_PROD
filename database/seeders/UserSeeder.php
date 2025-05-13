<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::first(); // Obtiene la primera sucursal que creamos

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'phone' => '123456789',
            'address' => 'Direccion Admin',
            'website' => 'https://example.com',
            'password' => Hash::make('admin123'),
            'branch_id' => $branch->id,  // Relaciona el usuario con una sucursal
        ])->assignRole('admin');

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '987654321',
            'address' => 'Direccion Test',
            'website' => 'https://test.com',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,  // Relaciona el usuario con una sucursal
        ])->assignRole('admin');
    }
}
