@extends('layouts.app')

@section('title', 'Terrains Disponibles - Réservation Sportive')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Terrains Disponibles</h1>

        <div class="row">
            @forelse($terrains as $terrain)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ $terrain->image_url }}" class="card-img-top terrain-image" alt="{{ $terrain->nom }}" style="height: 200px; object-fit: cover;" onerror="this.src='{{ asset('images/default-terrain.jpg') }}'">
                        <div class="card-body">
                            <h5 class="card-title">{{ $terrain->nom }}</h5>
                            <p class="card-text">
                                <strong>Type:</strong> {{ ucfirst($terrain->type) }}<br>
                                <strong>Prix:</strong> {{ $terrain->prix_heure }}€/heure
                            </p>
                            <a href="{{ route('terrains.show', $terrain) }}" class="btn btn-primary">
                                Voir les créneaux
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Aucun terrain disponible pour le moment.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>
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


