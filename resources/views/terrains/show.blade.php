@extends('layouts.app')

@section('title', $terrain->nom . ' - Créneaux Disponibles')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">{{ $terrain->nom }}</h1>

        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="{{ $terrain->image_url }}" class="img-fluid rounded-start terrain-image" alt="{{ $terrain->nom }}" onerror="this.src='{{ asset('images/default-terrain.jpg') }}'">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $terrain->nom }}</h5>
                        <p class="card-text">
                            <strong>Type:</strong> {{ ucfirst($terrain->type) }}<br>
                            <strong>Prix:</strong> {{ $terrain->prix_heure }}€/heure
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="mb-3">Créneaux disponibles</h2>
                <form method="GET" action="{{ route('terrains.show', $terrain) }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Sélectionner une date</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               value="{{ $date }}" 
                               min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::today()->addDays(30)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Voir les créneaux</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('terrains.show', ['terrain' => $terrain, 'date' => \Carbon\Carbon::today()->format('Y-m-d')]) }}" 
                           class="btn btn-outline-secondary">Aujourd'hui</a>
                        <a href="{{ route('terrains.show', ['terrain' => $terrain, 'date' => \Carbon\Carbon::tomorrow()->format('Y-m-d')]) }}" 
                           class="btn btn-outline-secondary">Demain</a>
                    </div>
                </form>
                <p class="text-muted mt-2 mb-0">
                    <strong>Date sélectionnée:</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <div class="row">
            @forelse($creneauxDisponibles as $creneau)
                <div class="col-md-3 mb-3">
                    <div class="card {{ $creneau['disponible'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $creneau['heure'] }}</h5>
                            @if($creneau['disponible'])
                                <span class="badge bg-success">Disponible</span>
                                <br>
                                <a href="{{ route('reservations.create', ['terrain' => $terrain, 'date' => $date, 'heure' => $creneau['heure']]) }}" class="btn btn-success btn-sm mt-2">
                                    Réserver
                                </a>
                            @else
                                <span class="badge bg-danger">Réservé</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        Aucun créneau disponible pour ce terrain.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            <a href="{{ route('terrains.index') }}" class="btn btn-secondary">Retour à la liste des terrains</a>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function reserverCreneau(heure) {
            document.getElementById('selectedCreneau').textContent = heure;
            new bootstrap.Modal(document.getElementById('reservationModal')).show();
        }

        // Gestionnaire d'erreurs pour les images de terrains
        document.addEventListener('DOMContentLoaded', function() {
            const terrainImages = document.querySelectorAll('.terrain-image');

            terrainImages.forEach(img => {
                img.addEventListener('error', function() {
                    if (!this.src.includes('default-terrain.jpg')) {
                        this.src = '{{ asset('images/default-terrain.jpg') }}';
                    }
                });
            });
        });
    </script>
@endsection


