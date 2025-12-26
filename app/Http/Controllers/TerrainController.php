<?php

namespace App\Http\Controllers;

use App\Models\Terrain;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TerrainController extends Controller
{
    public function index()
    {
        $terrains = Terrain::all();

        return view('terrains.index', compact('terrains'));
    }

    public function show(Terrain $terrain)
    {
        
        $creneauxDisponibles = $this->getCreneauxDisponibles($terrain);

        return view('terrains.show', compact('terrain', 'creneauxDisponibles'));
    }
//Verification des créneaux disponibles + chevauchement
    private function getCreneauxDisponibles(Terrain $terrain, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

       
        $creneaux = [];
        for ($heure = 8; $heure < 22; $heure++) {
            $creneaux[] = Carbon::createFromTime($heure, 0, 0)->format('H:i');
        }

       
        $reservations = Reservation::where('terrain_id', $terrain->id)
            ->where('date', $date->format('Y-m-d'))
            ->get();

       
        $creneauxDisponibles = [];
        foreach ($creneaux as $creneau) {
            $heureDebut = Carbon::createFromFormat('H:i', $creneau);
            $heureFin = $heureDebut->copy()->addHour();

            $isReserved = false;
            foreach ($reservations as $reservation) {
                $reservationDebut = Carbon::createFromFormat('H:i', $reservation->heure_debut);
                $reservationFin = $reservationDebut->copy()->addHours($reservation->duree);

                
                if ($heureDebut < $reservationFin && $heureFin > $reservationDebut) {
                    $isReserved = true;
                    break;
                }
            }

            $creneauxDisponibles[] = [
                'heure' => $creneau,
                'disponible' => !$isReserved
            ];
        }

        return $creneauxDisponibles;
    }
}
