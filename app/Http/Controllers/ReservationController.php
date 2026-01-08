<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Terrain;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    private function getDefaultUser()
    {
        return User::firstOrCreate(
            ['email' => 'client@default.com'],
            [
                'name' => 'Client par défaut',
                'telephone' => '0000000000',
                'password' => bcrypt('password'),
            ]
        );
    }

    public function index()
    {
        $reservations = Reservation::with(['terrain', 'equipements'])
            ->orderBy('date', 'desc')
            ->orderBy('heure_debut', 'desc')
            ->get();

        return view('reservations.index', compact('reservations'));
    }

    public function create(Terrain $terrain, Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $heure = $request->get('heure');

        $equipements = Equipement::all();

        return view('reservations.create', compact('terrain', 'date', 'heure', 'equipements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'terrain_id' => 'required|exists:terrains,id',
            'date' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'duree' => 'required|integer|min:1|max:8',
            'equipements' => 'nullable|array',
            'equipements.*.id' => 'exists:equipements,id',
            'equipements.*.quantite' => 'integer|min:1',
        ]);

        $terrain = Terrain::findOrFail($request->terrain_id);
        $heureDebut = Carbon::createFromFormat('H:i', $request->heure_debut);
        $heureFin = $heureDebut->copy()->addHours($request->duree);

        // Vérifier la disponibilité du terrain
        $conflictingReservation = Reservation::where('terrain_id', $request->terrain_id)
            ->where('date', $request->date)
            ->where(function ($query) use ($heureDebut, $heureFin) {
                $query->whereBetween('heure_debut', [$heureDebut->format('H:i'), $heureFin->format('H:i')])
                      ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                          $q->where('heure_debut', '<=', $heureDebut->format('H:i'))
                            ->whereRaw("DATE_ADD(heure_debut, INTERVAL duree HOUR) > ?", [$heureDebut->format('H:i')]);
                      });
            })
            ->first();

        if ($conflictingReservation) {
            return back()->withErrors(['disponibilite' => 'Ce créneau n\'est pas disponible.'])->withInput();
        }

        // Filtrer les équipements valides (avec id et quantite)
        $equipementsValides = [];
        if ($request->equipements) {
            $equipementsValides = array_filter($request->equipements, function($equipementData) {
                return !empty($equipementData['id']) && !empty($equipementData['quantite']);
            });
        }

        // Vérifier la disponibilité des équipements
        if (!empty($equipementsValides)) {
            foreach ($equipementsValides as $equipementData) {
                $equipement = Equipement::find($equipementData['id']);
                if (!$equipement || $equipement->quantite < $equipementData['quantite']) {
                    return back()->withErrors(['equipements' => "Quantité insuffisante pour {$equipement->nom}."])->withInput();
                }
            }
        }

        // Calculer le total
        $total = $terrain->prix_heure * $request->duree;

        if (!empty($equipementsValides)) {
            foreach ($equipementsValides as $equipementData) {
                // Supposons un prix fixe de 5€ par équipement pour cet exemple
                $total += 5 * $equipementData['quantite'];
            }
        }

        // Créer la réservation
        $user = $this->getDefaultUser();
        $reservation = Reservation::create([
            'client_id' => $user->id,
            'terrain_id' => $request->terrain_id,
            'date' => $request->date,
            'heure_debut' => $request->heure_debut,
            'duree' => $request->duree,
            'total' => $total,
        ]);

        // Attacher les équipements
        if (!empty($equipementsValides)) {
            foreach ($equipementsValides as $equipementData) {
                $reservation->equipements()->attach($equipementData['id'], [
                    'quantite' => $equipementData['quantite']
                ]);

                // Réduire le stock
                $equipement = Equipement::find($equipementData['id']);
                $equipement->decrement('quantite', $equipementData['quantite']);
            }
        }

        return redirect()->route('reservations.show', $reservation)->with('success', 'Réservation créée avec succès !');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['terrain', 'equipements']);

        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $equipements = Equipement::all();

        return view('reservations.edit', compact('reservation', 'equipements'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'duree' => 'required|integer|min:1|max:8',
            'equipements' => 'nullable|array',
            'equipements.*.id' => 'exists:equipements,id',
            'equipements.*.quantite' => 'integer|min:1',
        ]);

        $terrain = $reservation->terrain;
        $heureDebut = Carbon::createFromFormat('H:i', $request->heure_debut);
        $heureFin = $heureDebut->copy()->addHours($request->duree);

        // Vérifier la disponibilité (excluant la réservation actuelle)
        $conflictingReservation = Reservation::where('terrain_id', $terrain->id)
            ->where('date', $request->date)
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($heureDebut, $heureFin) {
                $query->whereBetween('heure_debut', [$heureDebut->format('H:i'), $heureFin->format('H:i')])
                      ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                          $q->where('heure_debut', '<=', $heureDebut->format('H:i'))
                            ->whereRaw("DATE_ADD(heure_debut, INTERVAL duree HOUR) > ?", [$heureDebut->format('H:i')]);
                      });
            })
            ->first();

        if ($conflictingReservation) {
            return back()->withErrors(['disponibilite' => 'Ce créneau n\'est pas disponible.'])->withInput();
        }

        // Remettre en stock les anciens équipements
        foreach ($reservation->equipements as $equipement) {
            $equipement->increment('quantite', $equipement->pivot->quantite);
        }

        // Vérifier la disponibilité des nouveaux équipements
        if ($request->equipements) {
            foreach ($request->equipements as $equipementData) {
                $equipement = Equipement::find($equipementData['id']);
                if ($equipement->quantite < $equipementData['quantite']) {
                    // Remettre les anciens équipements en stock
                    foreach ($reservation->equipements as $oldEquipement) {
                        $oldEquipement->increment('quantite', $oldEquipement->pivot->quantite);
                    }
                    return back()->withErrors(['equipements' => "Quantité insuffisante pour {$equipement->nom}."])->withInput();
                }
            }
        }

        // Calculer le nouveau total
        $total = $terrain->prix_heure * $request->duree;

        if ($request->equipements) {
            foreach ($request->equipements as $equipementData) {
                $total += 5 * $equipementData['quantite']; // Prix fixe par équipement
            }
        }

        // Mettre à jour la réservation
        $reservation->update([
            'date' => $request->date,
            'heure_debut' => $request->heure_debut,
            'duree' => $request->duree,
            'total' => $total,
        ]);

        // Synchroniser les équipements
        $reservation->equipements()->detach();

        if ($request->equipements) {
            foreach ($request->equipements as $equipementData) {
                $reservation->equipements()->attach($equipementData['id'], [
                    'quantite' => $equipementData['quantite']
                ]);

                // Réduire le stock
                $equipement = Equipement::find($equipementData['id']);
                $equipement->decrement('quantite', $equipementData['quantite']);
            }
        }

        return redirect()->route('reservations.show', $reservation)->with('success', 'Réservation modifiée avec succès !');
    }

    public function destroy(Reservation $reservation)
    {
        // Remettre les équipements en stock
        foreach ($reservation->equipements as $equipement) {
            $equipement->increment('quantite', $equipement->pivot->quantite);
        }

        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Réservation annulée avec succès !');
    }

    public function addEquipementsView(Reservation $reservation)
    {
        $dateString = $reservation->date->format('Y-m-d');
        $heureString = (string) $reservation->heure_debut;
        if (strlen($heureString) == 5) {
            $heureString .= ':00';
        }
        $dateReservation = Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $heureString);
        
        if (!$dateReservation->isFuture()) {
            return redirect()->route('reservations.show', $reservation)
                ->withErrors(['error' => 'Impossible d\'ajouter des équipements à une réservation passée.']);
        }

        $equipements = Equipement::where('quantite', '>', 0)->get();

        return view('reservations.add-equipements', compact('reservation', 'equipements'));
    }

    public function addEquipements(Request $request, Reservation $reservation)
    {
        $request->validate([
            'equipements' => 'nullable|array',
            'equipements.*.id' => 'required_with:equipements|exists:equipements,id',
            'equipements.*.quantite' => 'required_with:equipements.*.id|integer|min:1',
        ]);

        if (!$request->equipements || empty($request->equipements)) {
            return redirect()->route('reservations.show', $reservation)->with('info', 'Aucun équipement sélectionné.');
        }

        $equipementsValides = array_filter($request->equipements, function($equipementData) {
            return !empty($equipementData['id']) && !empty($equipementData['quantite']);
        });

        if (empty($equipementsValides)) {
            return redirect()->route('reservations.show', $reservation)->with('info', 'Aucun équipement valide sélectionné.');
        }

        // Vérifier la disponibilité des équipements
        foreach ($equipementsValides as $equipementData) {
            $equipement = Equipement::find($equipementData['id']);
            if (!$equipement || $equipement->quantite < $equipementData['quantite']) {
                return back()->withErrors(['equipements' => "Quantité insuffisante pour {$equipement->nom}."])->withInput();
            }
        }

        // Ajouter les équipements
        $totalAdditionnel = 0;
        foreach ($equipementsValides as $equipementData) {
            // Vérifier si l'équipement est déjà attaché
            $existingPivot = $reservation->equipements()->where('equipement_id', $equipementData['id'])->first();

            if ($existingPivot) {
                // Mettre à jour la quantité
                $nouvelleQuantite = $existingPivot->pivot->quantite + $equipementData['quantite'];
                $reservation->equipements()->updateExistingPivot($equipementData['id'], [
                    'quantite' => $nouvelleQuantite
                ]);
            } else {
                // Attacher le nouvel équipement
                $reservation->equipements()->attach($equipementData['id'], [
                    'quantite' => $equipementData['quantite']
                ]);
            }

            // Réduire le stock et calculer le total
            $equipement = Equipement::find($equipementData['id']);
            $equipement->decrement('quantite', $equipementData['quantite']);
            $totalAdditionnel += 5 * $equipementData['quantite']; // Prix fixe
        }

        // Mettre à jour le total
        $reservation->increment('total', $totalAdditionnel);

        return redirect()->route('reservations.show', $reservation)->with('success', 'Équipements ajoutés avec succès !');
    }
}
