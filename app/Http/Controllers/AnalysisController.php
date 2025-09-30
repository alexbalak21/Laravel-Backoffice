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
        $analyses = Analysis::latest()->paginate(15);
        
        return Inertia::render('Analyses/Index', [
            'analyses' => $analyses->through(function ($analysis) {
                return [
                    'id' => $analysis->id,
                    'numero_rapport' => $analysis->numero_rapport,
                    'date_heure_prelevement' => $analysis->date_heure_prelevement,
                    'date_heure_reception_laboratoire' => $analysis->date_heure_reception_laboratoire,
                    'temperature_reception' => $analysis->temperature_reception,
                    'date_heure_analyse' => $analysis->date_heure_analyse,
                    'fournisseur_fabricant' => $analysis->fournisseur_fabricant,
                    'conditionnement' => $analysis->conditionnement,
                    'lot' => $analysis->lot,
                    'type_peche' => $analysis->type_peche,
                    'nom_produit' => $analysis->nom_produit,
                    'espece' => $analysis->espece,
                    'origine' => $analysis->origine,
                    'date_emballage' => $analysis->date_emballage,
                    'date_consommation' => $analysis->date_consommation,
                    'imp' => $analysis->imp,
                    'hx' => $analysis->hx,
                    'cotation_fraicheur' => $analysis->cotation_fraicheur,
                    'ref_rapport' => $analysis->ref_rapport,
                ];
            }),
            'filters' => request()->only(['search']),
        ]);
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
        $validatedData = $request->validated();
        
        // Since we're getting an array of rows, we need to process each row
        $now = now();
        $dataToInsert = array_map(function ($row) use ($now) {
            return array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $validatedData);

        // Insert the data
        Analysis::insert($dataToInsert);
        
        return redirect()->route('analyses.index')->with('success', 'Data saved successfully');
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

            return response()->json([
                'message' => 'Data saved successfully',
                'saved_rows' => count($validatedData)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save data',
                'error' => $e->getMessage()
            ], 500);
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
