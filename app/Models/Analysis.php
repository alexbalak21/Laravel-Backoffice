<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'analyses';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_rapport',
        'lieu_prelevement',
        'date_heure_prelevement',
        'date_heure_reception_laboratoire',
        'temperature_reception',
        'conditions_conservation',
        'date_heure_analyse',
        'fournisseur_fabricant',
        'conditionnement',
        'agrement',
        'lot',
        'type_peche',
        'nom_produit',
        'espece',
        'origine',
        'date_emballage',
        'date_consommation',
        'imp',
        'hx',
        'note_nucleotide',
        'cotation_fraicheur',
        'observations',
        'ref_rapport',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_heure_prelevement' => 'datetime',
        'date_heure_reception_laboratoire' => 'datetime',
        'temperature_reception' => 'decimal:2',
        'date_heure_analyse' => 'datetime',
        'date_emballage' => 'date',
        'date_consommation' => 'date',
        'imp' => 'decimal:2',
        'hx' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
