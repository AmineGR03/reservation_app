<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Terrain extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'prix_heure',
        'image',
        'heure_ouverture',
        'heure_fermeture',
        'jours_fermeture',
    ];

    protected $casts = [
        'prix_heure' => 'decimal:2',
        'jours_fermeture' => 'array',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            $imagePath = storage_path('app/public/' . $this->image);
            if (file_exists($imagePath)) {
                return asset('storage/' . $this->image);
            }
        }

        return asset('images/default-terrain.jpg');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
