<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // este se encarga de ejecutar a otris seeders
        $this->call([
            RoleSeeder::class,
        ]);

        // usuario de prueba para el rol de administrador
        User::create([
            'username' => 'admin_test',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3,
        ]);

        // usuario de prueba para el rol de oyente
        User::create([
            'username' => 'oyente_test',
            'email' => 'oyente@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 1,
        ]);
    }
}