<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TerrainSeeder::class,
            EquipementSeeder::class,
        ]);

        // Créer un utilisateur de test
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'telephone' => '0123456789',
        ]);
    }
}
