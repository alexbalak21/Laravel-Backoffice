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
