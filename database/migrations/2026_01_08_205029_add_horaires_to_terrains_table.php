<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrains', function (Blueprint $table) {
            $table->time('heure_ouverture')->default('08:00:00')->after('prix_heure');
            $table->time('heure_fermeture')->default('22:00:00')->after('heure_ouverture');
            $table->json('jours_fermeture')->nullable()->after('heure_fermeture');
        });
    }

    public function down(): void
    {
        Schema::table('terrains', function (Blueprint $table) {
            $table->dropColumn(['heure_ouverture', 'heure_fermeture', 'jours_fermeture']);
        });
    }
};
