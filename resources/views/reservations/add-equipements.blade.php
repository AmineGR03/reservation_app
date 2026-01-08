@extends('layouts.app')

@section('title', 'Ajouter des Équipements')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Ajouter des équipements à la réservation</h3>
                        @php
                            $heureDebut = (string) $reservation->heure_debut;
                            $heureDebutFormatted = strlen($heureDebut) == 5 ? $heureDebut : substr($heureDebut, 0, 5);
                        @endphp
                        <p class="mb-0 text-muted">{{ $reservation->terrain->nom }} - {{ $reservation->date->format('d/m/Y') }} à {{ $heureDebutFormatted }}</p>
                    </div>
                    <div class="card-body">
                        @if($reservation->equipements->count() > 0)
                            <div class="mb-4">
                                <h5>Équipements déjà réservés</h5>
                                <div class="row">
                                    @foreach($reservation->equipements as $equipement)
                                        <div class="col-md-4 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">{{ $equipement->nom }}</h6>
                                                    <p class="card-text">Quantité: {{ $equipement->pivot->quantite }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('reservations.add-equipements', $reservation) }}" method="POST">
                            @csrf

                            <h5>Sélectionner les équipements à ajouter</h5>

                            @if($equipements->where('quantite', '>', 0)->isEmpty())
                                <div class="alert alert-info">
                                    Aucun équipement disponible en stock pour le moment.
                                </div>
                            @else
                                <div id="equipements-container">
                                    <div class="equipement-item mb-3 p-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <label class="form-label">Équipement</label>
                                                <select class="form-select equipement-select" name="equipements[0][id]">
                                                    <option value="">Choisir un équipement (optionnel)</option>
                                                    @foreach($equipements->where('quantite', '>', 0) as $equipement)
                                                        <option value="{{ $equipement->id }}" data-quantite="{{ $equipement->quantite }}">
                                                            {{ $equipement->nom }} (Stock disponible: {{ $equipement->quantite }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Quantité</label>
                                                <input type="number" class="form-control quantite-input"
                                                       name="equipements[0][quantite]" min="1" value="1" disabled>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Prix</label>
                                                <input type="text" class="form-control prix-display" value="5.00€" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger remove-equipement mt-4">
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary mb-3" id="add-equipement">
                                    + Ajouter un autre équipement
                                </button>

                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5>Résumé des ajouts</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Réservation actuelle:</strong> {{ number_format($reservation->total, 2) }}€</p>
                                                <p><strong>Ajout équipements:</strong> <span id="total-ajout">0.00€</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Nouveau total:</strong> <span id="nouveau-total">{{ number_format($reservation->total, 2) }}€</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('equipements')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">Ajouter les équipements (optionnel)</button>
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Annuler</a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let equipementIndex = 1;
        const currentTotal = {{ $reservation->total }};

        document.getElementById('add-equipement').addEventListener('click', function() {
            const container = document.getElementById('equipements-container');
            const newItem = document.querySelector('.equipement-item').cloneNode(true);

            // Update indices
            newItem.querySelectorAll('select, input').forEach(element => {
                const name = element.name;
                if (name) {
                    element.name = name.replace('[0]', '[' + equipementIndex + ']');
                    if (element.type === 'number') {
                        element.value = '1';
                        element.disabled = true;
                    } else if (element.classList.contains('form-select')) {
                        element.selectedIndex = 0;
                        element.required = false;
                    }
                }
            });

            container.appendChild(newItem);
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
                const item = e.target.closest('.equipement-item');
                const quantiteInput = item.querySelector('.quantite-input');
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

            if (e.target.classList.contains('quantite-input')) {
                updateTotals();
            }
        });

        function updateTotals() {
            let totalAjout = 0;
            let hasValidSelection = false;

            document.querySelectorAll('.equipement-item').forEach(item => {
                const select = item.querySelector('.equipement-select');
                const quantite = parseInt(item.querySelector('.quantite-input').value) || 0;

                if (select.value && quantite > 0) {
                    totalAjout += 5 * quantite;
                    hasValidSelection = true;
                }
            });

            const nouveauTotal = currentTotal + totalAjout;

            document.getElementById('total-ajout').textContent = totalAjout.toFixed(2) + '€';
            document.getElementById('nouveau-total').textContent = nouveauTotal.toFixed(2) + '€';

            // Update form submission - toujours activé car les équipements sont optionnels
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
        }

        updateTotals();
    </script>
@endsection
