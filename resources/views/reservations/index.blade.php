@extends('layouts.app')

@section('title', 'Mes Réservations - Système de Réservation Sportive')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Mes Réservations</h1>
            <a href="{{ route('terrains.index') }}" class="btn btn-primary">Réserver un terrain</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($reservations->isEmpty())
            <div class="text-center mt-5">
                <h3>Vous n'avez pas encore de réservations</h3>
                <p class="text-muted">Commencez par réserver un terrain sportif !</p>
                <a href="{{ route('terrains.index') }}" class="btn btn-primary btn-lg">Voir les terrains disponibles</a>
            </div>
        @else
            <div class="row">
                @foreach($reservations as $reservation)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title">{{ $reservation->terrain->nom }}</h5>
                                        <p class="card-text mb-1">
                                            @php
                                                $heureDebut = (string) $reservation->heure_debut;
                                                $heureDebutFormatted = strlen($heureDebut) == 5 ? $heureDebut : substr($heureDebut, 0, 5);
                                            @endphp
                                            <strong>Date:</strong> {{ $reservation->date->format('d/m/Y') }}<br>
                                            <strong>Heure:</strong> {{ $heureDebutFormatted }} ({{ $reservation->duree }}h)<br>
                                            <strong>Total:</strong> {{ number_format($reservation->total, 2) }}€
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $dateString = $reservation->date->format('Y-m-d');
                                            $heureString = (string) $reservation->heure_debut;
                                            if (strlen($heureString) == 5) {
                                                $heureString .= ':00';
                                            }
                                            $dateReservation = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $heureString);
                                            $now = \Carbon\Carbon::now();
                                            $isFuture = $dateReservation->isFuture();
                                            $canModify = $dateReservation->diffInHours($now) > 24;
                                        @endphp

                                        @if($isFuture)
                                            @if($canModify)
                                                <span class="badge bg-success mb-2">Modifiable</span>
                                            @else
                                                <span class="badge bg-warning mb-2">Bientôt</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary mb-2">Passée</span>
                                        @endif
                                    </div>
                                </div>

                                @if($reservation->equipements->count() > 0)
                                    <div class="mb-3">
                                        <strong>Équipements:</strong>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($reservation->equipements as $equipement)
                                                <li>• {{ $equipement->nom }} ({{ $equipement->pivot->quantite }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="d-flex gap-2">
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-info btn-sm">Détails</a>

                                    @if($isFuture && $canModify)
                                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning btn-sm">Modifier</a>
                                        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Annuler</button>
                                        </form>
                                    @endif

                                    @if($isFuture)
                                        <a href="{{ route('reservations.add-equipements', $reservation) }}" class="btn btn-success btn-sm">
                                            + Équipements
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('terrains.index') }}" class="btn btn-secondary">Retour aux terrains</a>
        </div>
    </div>
@endsection
