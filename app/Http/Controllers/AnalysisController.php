<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Http\Requests\StoreAnalysisDataRequest;
use App\Http\Requests\UpdateAnalysisRequest;
use Inertia\Inertia;

class AnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Analyses/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    

    /**
     * Store a newly created resource in storage.
     * @param StoreAnalysisDataRequest $request
     */
    public function store(StoreAnalysisDataRequest $request)
    {
        //
    }

    /**
     * Save analysis data from the spreadsheet
     *
     * @param StoreAnalysisDataRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Save analysis data from the spreadsheet
     *
     * @param StoreAnalysisDataRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveData(StoreAnalysisDataRequest $request)
    {
        try {
            // Get the processed and validated data
            $validatedData = $request->validatedData();
            
            // Add timestamps
            $now = now();
            $validatedData = array_map(function ($item) use ($now) {
                return array_merge($item, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }, $validatedData);

            // Insert all records in a single query
            Analysis::insert($validatedData);

            // Return a proper Inertia response
            return redirect()->back()->with([
                'success' => 'Data saved successfully',
                'saved_rows' => count($validatedData)
            ]);

        } catch (\Exception $e) {
            // Return a proper Inertia error response
            return back()->withErrors([
                'error' => 'Failed to save data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Analysis  $analysis
     * @return \Inertia\Response
     */
    public function show(Analysis $analysis)
    {
        return Inertia::render('Analyses/Show', [
            'analysis' => [
                'id' => $analysis->id,
                'numero_rapport' => $analysis->numero_rapport,
                'lieu_prelevement' => $analysis->lieu_prelevement,
                'date_heure_prelevement' => $analysis->date_heure_prelevement,
                'date_heure_reception_laboratoire' => $analysis->date_heure_reception_laboratoire,
                'temperature_reception' => $analysis->temperature_reception,
                'conditions_conservation' => $analysis->conditions_conservation,
                'date_heure_analyse' => $analysis->date_heure_analyse,
                'fournisseur_fabricant' => $analysis->fournisseur_fabricant,
                'conditionnement' => $analysis->conditionnement,
                'agrement' => $analysis->agrement,
                'lot' => $analysis->lot,
                'type_peche' => $analysis->type_peche,
                'nom_produit' => $analysis->nom_produit,
                'espece' => $analysis->espece,
                'origine' => $analysis->origine,
                'date_emballage' => $analysis->date_emballage,
                'date_consommation' => $analysis->date_consommation,
                'imp' => $analysis->imp,
                'hx' => $analysis->hx,
                'note_nucleotide' => $analysis->note_nucleotide,
                'cotation_fraicheur' => $analysis->cotation_fraicheur,
                'observations' => $analysis->observations,
                'ref_rapport' => $analysis->ref_rapport,
                'created_at' => $analysis->created_at,
                'updated_at' => $analysis->updated_at,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Analysis $analysis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnalysisRequest $request, Analysis $analysis)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Analysis $analysis)
    {
        //
    }
}
