<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Terrain;

class TerrainSeeder extends Seeder
{
    public function run(): void
    {
        $terrains = [
            [
                'nom' => 'Terrain de Football Principal',
                'type' => 'foot',
                'prix_heure' => 25.00,
            ],
            [
                'nom' => 'Terrain de Basketball Couvert',
                'type' => 'basket',
                'prix_heure' => 20.00,
            ],
            [
                'nom' => 'Court de Tennis 1',
                'type' => 'tennis',
                'prix_heure' => 15.00,
            ],
            [
                'nom' => 'Terrain de Volley Extérieur',
                'type' => 'volley',
                'prix_heure' => 18.00,
            ],
            [
                'nom' => 'Terrain de Handball',
                'type' => 'handball',
                'prix_heure' => 22.00,
            ],
        ];

        foreach ($terrains as $terrain) {
            Terrain::create($terrain);
        }
    }
}
