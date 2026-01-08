@extends('layouts.app')

@section('title', 'Administration - Terrains')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Terrains</h1>
            <a href="{{ route('admin.terrains.create') }}" class="btn btn-primary">Ajouter un terrain</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            @forelse($terrains as $terrain)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ $terrain->image_url }}" class="card-img-top terrain-image" alt="{{ $terrain->nom }}" style="height: 200px; object-fit: cover;" onerror="this.src='{{ asset('images/default-terrain.jpg') }}'">
                        <div class="card-body">
                            <h5 class="card-title">{{ $terrain->nom }}</h5>
                            <p class="card-text">
                                <strong>Type:</strong> {{ ucfirst($terrain->type) }}<br>
                                <strong>Prix:</strong> {{ $terrain->prix_heure }}€/heure<br>
                                @if($terrain->heure_ouverture && $terrain->heure_fermeture)
                                    <strong>Horaires:</strong> {{ substr($terrain->heure_ouverture, 0, 5) }} - {{ substr($terrain->heure_fermeture, 0, 5) }}<br>
                                @endif
                                @if($terrain->jours_fermeture && count($terrain->jours_fermeture) > 0)
                                    @php
                                        $joursNoms = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                        $joursFermes = array_map(function($j) use ($joursNoms) { return $joursNoms[$j]; }, $terrain->jours_fermeture);
                                    @endphp
                                    <strong>Fermé:</strong> {{ implode(', ', $joursFermes) }}
                                @endif
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('terrains.show', $terrain) }}" class="btn btn-info btn-sm" target="_blank">Voir public</a>
                                <a href="{{ route('admin.terrains.edit', $terrain) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('admin.terrains.destroy', $terrain) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce terrain ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Aucun terrain trouvé. <a href="{{ route('admin.terrains.create') }}">Créer le premier terrain</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            <a href="{{ route('terrains.index') }}" class="btn btn-secondary">Voir la liste publique</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
