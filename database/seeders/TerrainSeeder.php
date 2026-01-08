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
                'image' => 'terrains/football.jpg',
            ],
            [
                'nom' => 'Terrain de Basketball Couvert',
                'type' => 'basket',
                'prix_heure' => 20.00,
                'image' => 'terrains/basketball.jpg',
            ],
            [
                'nom' => 'Court de Tennis 1',
                'type' => 'tennis',
                'prix_heure' => 15.00,
                'image' => 'terrains/tennis.jpg',
            ],
            [
                'nom' => 'Terrain de Volley Extérieur',
                'type' => 'volley',
                'prix_heure' => 18.00,
                'image' => 'terrains/volley.jpg',
            ],
            [
                'nom' => 'Terrain de Handball',
                'type' => 'handball',
                'prix_heure' => 22.00,
                'image' => 'terrains/handball.jpg',
            ],
        ];

        foreach ($terrains as $terrain) {
            Terrain::create($terrain);
        }
    }
}
