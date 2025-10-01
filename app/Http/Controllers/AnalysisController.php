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
     *
     * @param StoreAnalysisDataRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAnalysisDataRequest $request)
    {
        try {
            // Get the processed and validated data
            $validatedData = $request->validatedData();
            
            // Add timestamps and ensure all fields are properly set
            $now = now();
            $records = [];
            
            // Define all possible fields in the correct order
            $allFields = [
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
                'created_at',
                'updated_at'
            ];
            
            foreach ($validatedData as $item) {
                // Initialize record with all fields set to null
                $record = array_fill_keys($allFields, null);
                
                // Merge with the actual data
                foreach ($item as $key => $value) {
                    if (in_array($key, $allFields)) {
                        $record[$key] = $value;
                    }
                }
                
                // Set timestamps
                $record['created_at'] = $now;
                $record['updated_at'] = $now;
                
                // Ensure required fields have values
                $requiredFields = [
                    'numero_rapport' => 'RAPPORT-' . now()->format('YmdHis'),
                    'ref_rapport' => 'REF-' . now()->format('YmdHis'),
                    'date_heure_prelevement' => $now,
                    'date_heure_reception_laboratoire' => $now,
                    'date_heure_analyse' => $now,
                    'date_emballage' => now()->format('Y-m-d'),
                    'date_consommation' => now()->addDays(7)->format('Y-m-d'),
                    'temperature_reception' => 0,
                    'imp' => 0,
                    'hx' => 0,
                ];
                
                foreach ($requiredFields as $field => $defaultValue) {
                    if (empty($record[$field])) {
                        $record[$field] = $defaultValue;
                    }
                }
                
                // Ensure numeric fields are properly cast
                $record['temperature_reception'] = (float)$record['temperature_reception'];
                $record['imp'] = (float)$record['imp'];
                $record['hx'] = (float)$record['hx'];
                
                // Ensure dates are properly formatted
                if ($record['date_heure_prelevement'] instanceof \Carbon\Carbon) {
                    $record['date_heure_prelevement'] = $record['date_heure_prelevement']->toDateTimeString();
                }
                
                if ($record['date_heure_reception_laboratoire'] instanceof \Carbon\Carbon) {
                    $record['date_heure_reception_laboratoire'] = $record['date_heure_reception_laboratoire']->toDateTimeString();
                }
                
                if ($record['date_heure_analyse'] instanceof \Carbon\Carbon) {
                    $record['date_heure_analyse'] = $record['date_heure_analyse']->toDateTimeString();
                }
                
                if ($record['date_emballage'] instanceof \Carbon\Carbon) {
                    $record['date_emballage'] = $record['date_emballage']->format('Y-m-d');
                }
                
                if ($record['date_consommation'] instanceof \Carbon\Carbon) {
                    $record['date_consommation'] = $record['date_consommation']->format('Y-m-d');
                }
                
                $records[] = $record;
                
                // Log the record for debugging
                \Log::debug('Processed record:', $record);
            }

            // Insert records one by one to get better error messages
            $savedCount = 0;
            foreach ($records as $record) {
                try {
                    Analysis::create($record);
                    $savedCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to save record: ' . $e->getMessage(), [
                        'record' => $record,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e; // Re-throw to be caught by the outer try-catch
                }
            }

            // Return a proper Inertia response with flash message
            return redirect()->back()->with([
                'flash' => [
                    'message' => [
                        'type' => 'success',
                        'text' => 'Data saved successfully',
                        'saved_rows' => $savedCount
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving analysis data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Return a proper Inertia error response with more details
            return back()->with([
                'flash' => [
                    'message' => [
                        'type' => 'error',
                        'details' => $e->getFile() . ' on line ' . $e->getLine()
                    ]
                ]
            ]);
        }
    }

    /**
     * Display the analyses list.
     *
     * @return \Inertia\Response
     */
    public function show()
    {
        $analyses = Analysis::latest()->get();
        
        return Inertia::render('Analyses/Show', [
            'analyses' => $analyses->map(function($analysis) {
                return [
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
                ];
            })->toArray()
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
