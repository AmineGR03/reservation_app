<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipementSeeder extends Seeder
{
    public function run(): void
    {
        $equipements = [
            [
                'nom' => 'Ballon de Football',
                'quantite' => 10,
            ],
            [
                'nom' => 'Ballon de Basketball',
                'quantite' => 8,
            ],
            [
                'nom' => 'Raquette de Tennis',
                'quantite' => 12,
            ],
            [
                'nom' => 'Ballon de Volley',
                'quantite' => 6,
            ],
            [
                'nom' => 'Ballon de Handball',
                'quantite' => 8,
            ],
            [
                'nom' => 'Maillots de Football (lot de 10)',
                'quantite' => 5,
            ],
            [
                'nom' => 'Sifflet d\'arbitre',
                'quantite' => 3,
            ],
            [
                'nom' => 'Chasubles de marquage',
                'quantite' => 20,
            ],
        ];

        foreach ($equipements as $equipement) {
            \App\Models\Equipement::create($equipement);
        }
    }
}
