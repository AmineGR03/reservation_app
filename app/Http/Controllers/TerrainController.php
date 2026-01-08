<?php

namespace App\Http\Controllers;

use App\Models\Terrain;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TerrainController extends Controller
{
    //list all
    public function index()
    {
        $terrains = Terrain::all();

        return view('terrains.index', compact('terrains'));
    }
//details
    public function show(Terrain $terrain, Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $creneauxDisponibles = $this->getCreneauxDisponibles($terrain, $date);

        return view('terrains.show', compact('terrain', 'creneauxDisponibles', 'date'));
    }
//Verification des créneaux disponibles + chevauchement
    private function getCreneauxDisponibles(Terrain $terrain, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $joursFermeture = $terrain->jours_fermeture ?? [];
        $jourSemaine = $date->dayOfWeek;
        
        if (in_array($jourSemaine, $joursFermeture)) {
            return [];
        }

        $heureOuvertureStr = $terrain->heure_ouverture ? (string) $terrain->heure_ouverture : '08:00:00';
        $heureFermetureStr = $terrain->heure_fermeture ? (string) $terrain->heure_fermeture : '22:00:00';
        
        $heureOuvertureStr = trim($heureOuvertureStr);
        $heureFermetureStr = trim($heureFermetureStr);
        
        if (strlen($heureOuvertureStr) == 5) {
            $heureOuvertureStr .= ':00';
        } elseif (strlen($heureOuvertureStr) > 8) {
            $heureOuvertureStr = substr($heureOuvertureStr, 0, 8);
        }
        
        if (strlen($heureFermetureStr) == 5) {
            $heureFermetureStr .= ':00';
        } elseif (strlen($heureFermetureStr) > 8) {
            $heureFermetureStr = substr($heureFermetureStr, 0, 8);
        }
        
        try {
            $heureOuverture = Carbon::createFromFormat('H:i:s', $heureOuvertureStr);
        } catch (\Exception $e) {
            $heureOuverture = Carbon::createFromTime(8, 0, 0);
        }
        
        try {
            $heureFermeture = Carbon::createFromFormat('H:i:s', $heureFermetureStr);
        } catch (\Exception $e) {
            $heureFermeture = Carbon::createFromTime(22, 0, 0);
        }

        $creneaux = [];
        $heureActuelle = $heureOuverture->copy();
        
        while ($heureActuelle < $heureFermeture) {
            $creneaux[] = $heureActuelle->format('H:i');
            $heureActuelle->addHour();
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
                $heureDebutStr = trim((string) $reservation->heure_debut);
                
                try {
                    if (strlen($heureDebutStr) == 5) {
                        $reservationDebut = Carbon::createFromFormat('H:i', $heureDebutStr);
                    } elseif (strlen($heureDebutStr) >= 8) {
                        $reservationDebut = Carbon::createFromFormat('H:i:s', substr($heureDebutStr, 0, 8));
                    } else {
                        continue;
                    }
                } catch (\Exception $e) {
                    continue;
                }
                
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

    public function adminIndex()
    {
        $terrains = Terrain::all();
        return view('admin.terrains.index', compact('terrains'));
    }

    public function create()
    {
        return view('admin.terrains.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:foot,basket,tennis,volley,handball',
            'prix_heure' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'heure_ouverture' => 'required|date_format:H:i',
            'heure_fermeture' => 'required|date_format:H:i|after:heure_ouverture',
            'jours_fermeture' => 'nullable|array',
            'jours_fermeture.*' => 'integer|min:0|max:6',
        ]);

        $data = $request->only(['nom', 'type', 'prix_heure', 'heure_ouverture', 'heure_fermeture', 'jours_fermeture']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('terrains', 'public');
            $data['image'] = $imagePath;
        }

        if (isset($data['jours_fermeture'])) {
            $data['jours_fermeture'] = array_map('intval', $data['jours_fermeture']);
        }

        Terrain::create($data);

        return redirect()->route('admin.terrains.index')->with('success', 'Terrain créé avec succès.');
    }

    public function edit(Terrain $terrain)
    {
        return view('admin.terrains.edit', compact('terrain'));
    }

    public function update(Request $request, Terrain $terrain)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:foot,basket,tennis,volley,handball',
            'prix_heure' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'heure_ouverture' => 'required|date_format:H:i',
            'heure_fermeture' => 'required|date_format:H:i|after:heure_ouverture',
            'jours_fermeture' => 'nullable|array',
            'jours_fermeture.*' => 'integer|min:0|max:6',
        ]);

        $data = $request->only(['nom', 'type', 'prix_heure', 'heure_ouverture', 'heure_fermeture', 'jours_fermeture']);

        if ($request->hasFile('image')) {
            if ($terrain->image) {
                Storage::disk('public')->delete($terrain->image);
            }
            $imagePath = $request->file('image')->store('terrains', 'public');
            $data['image'] = $imagePath;
        }

        if (isset($data['jours_fermeture'])) {
            $data['jours_fermeture'] = array_map('intval', $data['jours_fermeture']);
        } else {
            $data['jours_fermeture'] = [];
        }

        $terrain->update($data);

        return redirect()->route('admin.terrains.index')->with('success', 'Terrain modifié avec succès.');
    }

    public function destroy(Terrain $terrain)
    {
        if ($terrain->image) {
            Storage::disk('public')->delete($terrain->image);
        }

        $terrain->delete();

        return redirect()->route('admin.terrains.index')->with('success', 'Terrain supprimé avec succès.');
    }
}
