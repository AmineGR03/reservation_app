@extends('layouts.app')

@section('title', 'Modifier la Réservation')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Modifier la réservation - {{ $reservation->terrain->nom }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reservations.update', $reservation) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date *</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                           id="date" name="date" value="{{ old('date', $reservation->date->format('Y-m-d')) }}"
                                           min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="heure_debut" class="form-label">Heure de début *</label>
                                    <select class="form-select @error('heure_debut') is-invalid @enderror"
                                            id="heure_debut" name="heure_debut" required>
                                        <option value="">Choisir une heure</option>
                                        @for($i = 8; $i < 22; $i++)
                                            @php
                                                $heureOption = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                                                $heureActuelle = (string) $reservation->heure_debut;
                                                $heureActuelleFormatted = strlen($heureActuelle) == 5 ? $heureActuelle : substr($heureActuelle, 0, 5);
                                                $isSelected = old('heure_debut', $heureActuelleFormatted) == $heureOption;
                                            @endphp
                                            <option value="{{ $heureOption }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $heureOption }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('heure_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="duree" class="form-label">Durée (heures) *</label>
                                <select class="form-select @error('duree') is-invalid @enderror"
                                        id="duree" name="duree" required>
                                    <option value="">Choisir la durée</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('duree', $reservation->duree) == $i ? 'selected' : '' }}>
                                            {{ $i }} heure{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('duree')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Équipements</h5>
                                </div>
                                <div class="card-body">
                                    @if($reservation->equipements->count() > 0)
                                        <div class="mb-3">
                                            <h6>Équipements actuels:</h6>
                                            <ul class="list-group mb-3">
                                                @foreach($reservation->equipements as $equipement)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $equipement->nom }}
                                                        <span class="badge bg-primary rounded-pill">{{ $equipement->pivot->quantite }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="text-muted small">Les équipements peuvent être modifiés ci-dessous. Les changements seront appliqués au stock.</p>
                                        </div>
                                    @endif

                                    @if($equipements->isEmpty())
                                        <p class="text-muted">Aucun équipement disponible pour le moment.</p>
                                    @else
                                        <div id="equipements-container">
                                            @foreach($reservation->equipements as $index => $equipement)
                                                <div class="equipement-item mb-3 p-3 border rounded">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-5">
                                                            <label class="form-label">Équipement</label>
                                                            <select class="form-select equipement-select" name="equipements[{{ $index }}][id]">
                                                                <option value="">Choisir un équipement</option>
                                                                @foreach($equipements as $eq)
                                                                    <option value="{{ $eq->id }}"
                                                                            data-quantite="{{ $eq->quantite + ($eq->id == $equipement->id ? $equipement->pivot->quantite : 0) }}"
                                                                            {{ $eq->id == $equipement->id ? 'selected' : '' }}>
                                                                        {{ $eq->nom }} (Stock disponible: {{ $eq->quantite + ($eq->id == $equipement->id ? $equipement->pivot->quantite : 0) }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Quantité</label>
                                                            <input type="number" class="form-control quantite-input"
                                                                   name="equipements[{{ $index }}][quantite]" min="1"
                                                                   value="{{ $equipement->pivot->quantite }}" {{ $equipement->id ? '' : 'disabled' }}>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label">Prix</label>
                                                            <input type="text" class="form-control prix-display" value="5.00€" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger remove-equipement mt-4">
                                                                Supprimer
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-outline-primary" id="add-equipement">
                                            + Ajouter un équipement
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Résumé de la modification</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Terrain:</strong> {{ $reservation->terrain->nom }}</p>
                                                <p><strong>Prix/heure:</strong> {{ $reservation->terrain->prix_heure }}€</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Équipements:</strong> <span id="total-equipements">0.00€</span></p>
                                                <p><strong>Nouveau total estimé:</strong> <span id="total-estime">{{ number_format($reservation->terrain->prix_heure, 2) }}€</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('disponibilite')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            @error('equipements')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Mettre à jour la réservation</button>
                                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let equipementIndex = {{ $reservation->equipements->count() }};

        document.getElementById('add-equipement').addEventListener('click', function() {
            const container = document.getElementById('equipements-container');
            const template = `
                <div class="equipement-item mb-3 p-3 border rounded">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <label class="form-label">Équipement</label>
                            <select class="form-select equipement-select" name="equipements[${equipementIndex}][id]">
                                <option value="">Choisir un équipement</option>
                                @foreach($equipements as $equipement)
                                    <option value="{{ $equipement->id }}" data-quantite="{{ $equipement->quantite }}">
                                        {{ $equipement->nom }} (Stock: {{ $equipement->quantite }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" class="form-control quantite-input" name="equipements[${equipementIndex}][quantite]" min="1" value="1" disabled>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Prix</label>
                            <input type="text" class="form-control prix-display" value="5.00€" readonly>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-equipement mt-4">Supprimer</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            equipementIndex++;
            updateTotals();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-equipement')) {
                if (document.querySelectorAll('.equipement-item').length > 1) {
                    e.target.closest('.equipement-item').remove();
                    updateTotals();
                }
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('equipement-select')) {
                const quantiteInput = e.target.closest('.equipement-item').querySelector('.quantite-input');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const maxQuantite = selectedOption.getAttribute('data-quantite');

                if (maxQuantite && maxQuantite > 0) {
                    quantiteInput.disabled = false;
                    quantiteInput.max = maxQuantite;
                    quantiteInput.value = Math.min(quantiteInput.value, maxQuantite);
                } else {
                    quantiteInput.disabled = true;
                    quantiteInput.value = '';
                }
                updateTotals();
            }

            if (e.target.classList.contains('quantite-input') || e.target.id === 'duree') {
                updateTotals();
            }
        });

        function updateTotals() {
            const duree = parseInt(document.getElementById('duree').value) || 0;
            const prixHeure = {{ $reservation->terrain->prix_heure }};
            let totalEquipements = 0;

            document.querySelectorAll('.equipement-item').forEach(item => {
                const select = item.querySelector('.equipement-select');
                const quantite = parseInt(item.querySelector('.quantite-input').value) || 0;

                if (select.value && quantite > 0) {
                    totalEquipements += 5 * quantite;
                }
            });

            const totalTerrain = duree * prixHeure;
            const totalGeneral = totalTerrain + totalEquipements;

            document.getElementById('total-equipements').textContent = totalEquipements.toFixed(2) + '€';
            document.getElementById('total-estime').textContent = totalGeneral.toFixed(2) + '€';
        }

        updateTotals();
    </script>
@endsection
