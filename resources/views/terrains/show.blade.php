<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $terrain->nom }} - Créneaux Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">{{ $terrain->nom }}</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Informations du terrain</h5>
                <p class="card-text">
                    <strong>Type:</strong> {{ ucfirst($terrain->type) }}<br>
                    <strong>Prix:</strong> {{ $terrain->prix_heure }}€/heure
                </p>
            </div>
        </div>

        <h2 class="mb-3">Créneaux disponibles aujourd'hui</h2>

        <div class="row">
            @forelse($creneauxDisponibles as $creneau)
                <div class="col-md-3 mb-3">
                    <div class="card {{ $creneau['disponible'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $creneau['heure'] }}</h5>
                            @if($creneau['disponible'])
                                <span class="badge bg-success">Disponible</span>
                                <br>
                                <button class="btn btn-success btn-sm mt-2" onclick="reserverCreneau('{{ $creneau['heure'] }}')">
                                    Réserver
                                </button>
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

    <!-- Modal de réservation (pour plus tard) -->
    <div class="modal fade" id="reservationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Réserver le terrain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Fonctionnalité de réservation à implémenter...</p>
                    <p><strong>Créneau:</strong> <span id="selectedCreneau"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function reserverCreneau(heure) {
            document.getElementById('selectedCreneau').textContent = heure;
            new bootstrap.Modal(document.getElementById('reservationModal')).show();
        }
    </script>
</body>
</html>
