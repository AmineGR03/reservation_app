<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerrainController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes pour les terrains
Route::get('/terrains', [TerrainController::class, 'index'])->name('terrains.index');
//details d'un terrain
Route::get('/terrains/{terrain}', [TerrainController::class, 'show'])->name('terrains.show');

// Routes d'administration
Route::get('/admin/terrains', [TerrainController::class, 'adminIndex'])->name('admin.terrains.index');
Route::get('/admin/terrains/create', [TerrainController::class, 'create'])->name('admin.terrains.create');
Route::post('/admin/terrains', [TerrainController::class, 'store'])->name('admin.terrains.store');
Route::get('/admin/terrains/{terrain}/edit', [TerrainController::class, 'edit'])->name('admin.terrains.edit');
Route::put('/admin/terrains/{terrain}', [TerrainController::class, 'update'])->name('admin.terrains.update');
Route::delete('/admin/terrains/{terrain}', [TerrainController::class, 'destroy'])->name('admin.terrains.destroy');

// Routes de réservation
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::get('/reservations/create/{terrain}', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

// Routes pour ajouter des équipements à une réservation existante
Route::get('/reservations/{reservation}/add-equipements', [ReservationController::class, 'addEquipementsView'])->name('reservations.add-equipements');
Route::post('/reservations/{reservation}/equipements', [ReservationController::class, 'addEquipements'])->name('reservations.add-equipements.store');
