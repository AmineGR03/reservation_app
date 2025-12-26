<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'terrain_id',
        'date',
        'heure_debut',
        'duree',
        'total',
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime:H:i',
        'total' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function terrain(): BelongsTo
    {
        return $this->belongsTo(Terrain::class);
    }
//pivot
    public function equipements(): BelongsToMany
    {
        return $this->belongsToMany(Equipement::class, 'reservation_equipement')
                    ->withPivot('quantite')
                    ->withTimestamps();
    }
}
