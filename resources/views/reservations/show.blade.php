@extends('layouts.app')

@section('title', 'Détails de la Réservation')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Détails de la réservation</h3>
                        <div>
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

                            @if($isFuture && $canModify)
                                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="{{ route('reservations.add-equipements', $reservation) }}" class="btn btn-success btn-sm">+ Équipements</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Informations générales</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Terrain:</strong></td>
                                        <td>{{ $reservation->terrain->nom }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>{{ ucfirst($reservation->terrain->type) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Heure:</strong></td>
                                        <td>
                                            @php
                                                $heureDebut = (string) $reservation->heure_debut;
                                                $heureDebutFormatted = strlen($heureDebut) == 5 ? $heureDebut : substr($heureDebut, 0, 5);
                                                $heureFin = \Carbon\Carbon::createFromFormat('H:i', $heureDebutFormatted)->addHours($reservation->duree)->format('H:i');
                                            @endphp
                                            {{ $heureDebutFormatted }} - {{ $heureFin }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Durée:</strong></td>
                                        <td>{{ $reservation->duree }} heure{{ $reservation->duree > 1 ? 's' : '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Statut:</strong></td>
                                        <td>
                                            @if($isFuture)
                                                @if($canModify)
                                                    <span class="badge bg-success">Modifiable</span>
                                                @else
                                                    <span class="badge bg-warning">Confirmée</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Terminée</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Détail des frais</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Terrain ({{ $reservation->duree }}h × {{ $reservation->terrain->prix_heure }}€):</td>
                                        <td class="text-end">{{ number_format($reservation->duree * $reservation->terrain->prix_heure, 2) }}€</td>
                                    </tr>
                                    @if($reservation->equipements->count() > 0)
                                        @php $totalEquipements = 0; @endphp
                                        @foreach($reservation->equipements as $equipement)
                                            @php $totalEquipements += 5 * $equipement->pivot->quantite; @endphp
                                            <tr>
                                                <td>{{ $equipement->nom }} ({{ $equipement->pivot->quantite }} × 5.00€):</td>
                                                <td class="text-end">{{ number_format(5 * $equipement->pivot->quantite, 2) }}€</td>
                                            </tr>
                                        @endforeach
                                        <tr class="border-top">
                                            <td><strong>Total équipements:</strong></td>
                                            <td class="text-end"><strong>{{ number_format($totalEquipements, 2) }}€</strong></td>
                                        </tr>
                                    @endif
                                    <tr class="border-top border-2">
                                        <td><strong>TOTAL:</strong></td>
                                        <td class="text-end"><strong class="text-primary">{{ number_format($reservation->total, 2) }}€</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($reservation->equipements->count() > 0)
                            <div class="mt-4">
                                <h5>Équipements réservés</h5>
                                <div class="row">
                                    @foreach($reservation->equipements as $equipement)
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">{{ $equipement->nom }}</h6>
                                                    <p class="card-text">
                                                        Quantité: {{ $equipement->pivot->quantite }}<br>
                                                        Prix: {{ number_format(5 * $equipement->pivot->quantite, 2) }}€
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($isFuture && $canModify)
                            <div class="mt-4 border-top pt-3">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">Modifier la réservation</a>
                                    <a href="{{ route('reservations.add-equipements', $reservation) }}" class="btn btn-success">Ajouter des équipements</a>
                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Annuler la réservation</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Retour à mes réservations</a>
            <a href="{{ route('terrains.index') }}" class="btn btn-primary">Voir d'autres terrains</a>
        </div>
    </div>

@endsection
