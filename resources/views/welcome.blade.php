@extends('layouts.app')

@section('title', 'Accueil - Système de Réservation Sportive')

@section('content')
<div class="container-fluid px-4">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-primary mb-3">
            🏆 Système de Réservation Sportive
        </h1>
        <p class="lead text-muted mb-4">
            Réservez vos terrains sportifs préférés en quelques clics !
        </p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-check-circle text-success" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 1 1.06 1.06L6.97 11.03a.75.75 0 0 1-1.079-.02l-3.992-4.99a.75.75 0 0 1 1.071-1.05z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="card-title text-center">Réservation Simple</h5>
                    <p class="card-text">Choisissez votre terrain, sélectionnez un créneau disponible et réservez en quelques secondes.</p>
                    <div class="mt-auto">
                        <a href="{{ route('terrains.index') }}" class="btn btn-success w-100">Voir les terrains</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-calendar-check text-primary" viewBox="0 0 16 16">
                                <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="card-title text-center">Gestion Complète</h5>
                    <p class="card-text">Gérez vos réservations : consultez, modifiez ou annulez selon vos besoins.</p>
                    <div class="mt-auto">
                        <a href="{{ route('reservations.index') }}" class="btn btn-primary w-100">Mes réservations</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-basket text-warning" viewBox="0 0 16 16">
                                <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5c0 .562-.214 1.082-.6 1.46-.364.32-.835.54-1.31.611L12.5 13h-9l-.14-1.09c-.475-.07-.946-.29-1.31-.611C1.714 9.582 1.5 9.062 1.5 8.5a.5.5 0 0 1 .5-.5h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM3.394 6l1.68 5H4.25a.5.5 0 0 0 0 1h7.5a.5.5 0 0 0 0-1H10.93l1.68-5H3.394z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="card-title text-center">Équipements Disponibles</h5>
                    <p class="card-text">Ballons, raquettes, maillots... Ajoutez des équipements à vos réservations.</p>
                    <div class="mt-auto">
                        <a href="{{ route('terrains.index') }}" class="btn btn-warning w-100">Réserver avec équipements</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-gradient-to-r from-green-400 to-blue-500 text-white">
                <div class="card-body text-center py-5">
                    <h2 class="card-title h3 mb-3">Prêt à réserver votre terrain ?</h2>
                    <p class="card-text mb-4">Réservez dès maintenant vos terrains sportifs préférés !</p>
                    <a href="{{ route('terrains.index') }}" class="btn btn-light btn-lg">Commencer maintenant</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
