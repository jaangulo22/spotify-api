<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Pop', 'Rock', 'Hip Hop', 'Reggaeton', 'Electrónica', 
            'Jazz', 'Clásica', 'Regional Mexicano', 'Indie'
        ];

        foreach ($genres as $genre) {
            // se usa este para evitar duplicados
            Genre::firstOrCreate(['name' => $genre]); 
        }
    }
}
