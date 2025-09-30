<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('numero_rapport')->nullable()->change();
            $table->string('lieu_prelevement')->nullable()->change();
            $table->dateTime('date_heure_prelevement')->nullable()->change();
            $table->dateTime('date_heure_reception_laboratoire')->nullable()->change();
            $table->decimal('temperature_reception', 8, 2)->nullable()->change();
            $table->string('fournisseur_fabricant')->nullable()->change();
            $table->string('conditionnement')->nullable()->change();
            $table->string('agrement')->nullable()->change();
            $table->string('lot')->nullable()->change();
            $table->string('type_peche')->nullable()->change();
            $table->string('nom_produit')->nullable()->change();
            $table->string('espece')->nullable()->change();
            $table->string('origine')->nullable()->change();
            $table->date('date_emballage')->nullable()->change();
            $table->date('date_consommation')->nullable()->change();
            $table->decimal('imp', 8, 2)->nullable()->change();
            $table->decimal('hx', 8, 2)->nullable()->change();
            $table->string('cotation_fraicheur')->nullable()->change();
            $table->string('ref_rapport')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('numero_rapport')->nullable(false)->change();
            $table->string('lieu_prelevement')->nullable(false)->change();
            $table->dateTime('date_heure_prelevement')->nullable(false)->change();
            $table->dateTime('date_heure_reception_laboratoire')->nullable(false)->change();
            $table->decimal('temperature_reception', 8, 2)->nullable(false)->change();
            $table->string('fournisseur_fabricant')->nullable(false)->change();
            $table->string('conditionnement')->nullable(false)->change();
            $table->string('agrement')->nullable(false)->change();
            $table->string('lot')->nullable(false)->change();
            $table->string('type_peche')->nullable(false)->change();
            $table->string('nom_produit')->nullable(false)->change();
            $table->string('espece')->nullable(false)->change();
            $table->string('origine')->nullable(false)->change();
            $table->date('date_emballage')->nullable(false)->change();
            $table->date('date_consommation')->nullable(false)->change();
            $table->decimal('imp', 8, 2)->nullable(false)->change();
            $table->decimal('hx', 8, 2)->nullable(false)->change();
            $table->string('cotation_fraicheur')->nullable(false)->change();
            $table->string('ref_rapport')->nullable(false)->change();
        });
    }
};
