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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->string('numero_rapport');
            $table->string('lieu_prelevement');
            $table->dateTime('date_heure_prelevement');
            $table->dateTime('date_heure_reception_laboratoire');
            $table->decimal('temperature_reception', 8, 2);
            $table->text('conditions_conservation')->nullable();
            $table->dateTime('date_heure_analyse');
            $table->string('fournisseur_fabricant');
            $table->string('conditionnement');
            $table->string('agrement');
            $table->string('lot');
            $table->string('type_peche');
            $table->string('nom_produit');
            $table->string('espece');
            $table->string('origine');
            $table->date('date_emballage');
            $table->date('date_consommation');
            $table->decimal('imp', 8, 2);
            $table->decimal('hx', 8, 2);
            $table->text('note_nucleotide')->nullable();
            $table->string('cotation_fraicheur');
            $table->text('observations')->nullable();
            $table->string('ref_rapport');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
