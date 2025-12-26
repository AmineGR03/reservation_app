<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terrains Disponibles - Réservation Sportive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Terrains Disponibles</h1>

        <div class="row">
            @forelse($terrains as $terrain)
                <div class="col-md-4 mb-4">
                    <div class="card">
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
            <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
