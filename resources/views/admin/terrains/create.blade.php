@extends('layouts.app')

@section('title', 'Ajouter un Terrain')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Ajouter un nouveau terrain</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.terrains.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom du terrain *</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type de terrain *</label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Choisir un type</option>
                                    <option value="foot" {{ old('type') == 'foot' ? 'selected' : '' }}>Football</option>
                                    <option value="basket" {{ old('type') == 'basket' ? 'selected' : '' }}>Basketball</option>
                                    <option value="tennis" {{ old('type') == 'tennis' ? 'selected' : '' }}>Tennis</option>
                                    <option value="volley" {{ old('type') == 'volley' ? 'selected' : '' }}>Volley-ball</option>
                                    <option value="handball" {{ old('type') == 'handball' ? 'selected' : '' }}>Handball</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="prix_heure" class="form-label">Prix par heure (€) *</label>
                                <input type="number" step="0.01" class="form-control @error('prix_heure') is-invalid @enderror"
                                       id="prix_heure" name="prix_heure" value="{{ old('prix_heure') }}" required>
                                @error('prix_heure')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image du terrain</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                       id="image" name="image" accept="image/*">
                                <div class="form-text">Formats acceptés: JPEG, PNG, JPG, GIF. Taille max: 2MB</div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Gestion des créneaux</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="heure_ouverture" class="form-label">Heure d'ouverture *</label>
                                    <input type="time" class="form-control @error('heure_ouverture') is-invalid @enderror"
                                           id="heure_ouverture" name="heure_ouverture" 
                                           value="{{ old('heure_ouverture', '08:00') }}" required>
                                    @error('heure_ouverture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="heure_fermeture" class="form-label">Heure de fermeture *</label>
                                    <input type="time" class="form-control @error('heure_fermeture') is-invalid @enderror"
                                           id="heure_fermeture" name="heure_fermeture" 
                                           value="{{ old('heure_fermeture', '22:00') }}" required>
                                    @error('heure_fermeture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jours de fermeture</label>
                                <div class="row">
                                    @php
                                        $jours = [
                                            0 => 'Dimanche',
                                            1 => 'Lundi',
                                            2 => 'Mardi',
                                            3 => 'Mercredi',
                                            4 => 'Jeudi',
                                            5 => 'Vendredi',
                                            6 => 'Samedi'
                                        ];
                                    @endphp
                                    @foreach($jours as $num => $jour)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="jours_fermeture[]" 
                                                       value="{{ $num }}" 
                                                       id="jour_{{ $num }}"
                                                       {{ in_array($num, old('jours_fermeture', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="jour_{{ $num }}">
                                                    {{ $jour }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Cochez les jours où le terrain est fermé</div>
                                @error('jours_fermeture')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Créer le terrain</button>
                                <a href="{{ route('admin.terrains.index') }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
