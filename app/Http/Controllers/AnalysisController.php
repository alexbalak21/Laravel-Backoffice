<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Http\Requests\StoreAnalysisRequest;
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
     */
    public function store(StoreAnalysisRequest $request)
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
     */
    public function show(Analysis $analysis)
    {
        //
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
