<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StoreAnalysisDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update this based on your auth requirements
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.Numéro Rapport' => 'required|string|max:255',
            '*.Date et lieu de prélèvement' => 'required|string|max:255',
            '*.Date, heure et T°C à la réception au laboratoire' => 'required|string|max:255',
            '*.Conditions de conservation à la réception' => 'nullable|string|max:255',
            '*.Date de mise en analyse' => 'required|string|max:255',
            '*.Fournisseur/Fabricant' => 'required|string|max:255',
            '*.Conditionnement' => 'required|string|max:255',
            '*.Agrément' => 'nullable|string|max:255',
            '*.Lot' => 'required|string|max:255',
            '*.Type de pèche' => 'required|string|max:255',
            '*.Nom de produit' => 'required|string|max:255',
            '*.Espèce' => 'required|string|max:255',
            '*.Origine' => 'required|string|max:255',
            '*.Date d\'emballage' => 'nullable|string|max:255',
            '*.A consommer jusqu\'au' => 'nullable|string|max:255',
            '*.IMP' => 'nullable|string|max:255',
            '*.HX' => 'nullable|string|max:255',
            '*.Note Nucléotide' => 'nullable|string|max:255',
            '*.Cotation fraîcheur' => 'nullable|string|max:255',
            '*.Observations' => 'nullable|string',
            '*.Ref Rapport' => 'nullable|string|max:255',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $processedData = [];
        
        foreach ($this->all() as $row) {
            $processedRow = [];
            
            // Clean all string values
            foreach ($row as $key => $value) {
                $processedRow[$key] = is_string($value) ? trim($value) : $value;
            }
            
            $processedData[] = $processedRow;
        }
        
        $this->merge($processedData);
    }
    
    /**
     * Get the validated data that was processed.
     *
     * @return array
     */
    public function validatedData(): array
    {
        $validated = $this->validated();
        $processedData = [];
        
        foreach ($validated as $row) {
            // Parse prelevement data (e.g., "COPROMER, 27/08/2025, 11h30")
            $prelevement = $this->parsePrelevementData($row['Date et lieu de prélèvement']);
            
            // Parse reception data (e.g., "25/08/2025, 11h45, 4°C")
            $reception = $this->parseReceptionData($row['Date, heure et T°C à la réception au laboratoire']);
            
            // Parse analysis date (e.g., "25/08/2025, 13h")
            $analysisDate = $this->parseDateTime($row['Date de mise en analyse']);
            
            // Parse temperature from reception conditions (e.g., "0-2°C")
            $tempMatch = [];
            $minTemp = null;
            if (preg_match('/(\d+)[-–]?(\d+)?°C/i', $row['Conditions de conservation à la réception'] ?? '', $tempMatch)) {
                $minTemp = isset($tempMatch[2]) ? (float)$tempMatch[1] : (float)$tempMatch[1];
            }
            
            // Parse numeric values from IMP and HX (e.g., "1%" => 1.0)
            $imp = is_numeric(trim($row['IMP'] ?? '', "% ")) ? (float)trim($row['IMP'], "% ") : null;
            $hx = is_numeric(trim($row['HX'] ?? '', "% ")) ? (float)trim($row['HX'], "% ") : null;
            
            // Parse dates
            $emballageDate = !empty($row['Date d\'emballage']) ? Carbon::createFromFormat('d/m/Y', trim($row['Date d\'emballage']))->format('Y-m-d') : null;
            $consommationDate = !empty($row['A consommer jusqu\'au']) ? Carbon::createFromFormat('d-m-y', trim($row['A consommer jusqu\'au']))->format('Y-m-d') : null;
            
            $processedData[] = [
                'numero_rapport' => $row['Numéro Rapport'],
                'lieu_prelevement' => $prelevement['lieu'] ?? null,
                'date_heure_prelevement' => $prelevement['date_heure'] ?? null,
                'date_heure_reception_laboratoire' => $reception['date_heure'] ?? null,
                'temperature_reception' => $reception['temperature'] ?? null,
                'conditions_conservation' => $row['Conditions de conservation à la réception'] ?? null,
                'date_heure_analyse' => $analysisDate,
                'fournisseur_fabricant' => $row['Fournisseur/Fabricant'],
                'conditionnement' => $row['Conditionnement'],
                'agrement' => $row['Agrément'] ?? null,
                'lot' => $row['Lot'],
                'type_peche' => $row['Type de pèche'],
                'nom_produit' => $row['Nom de produit'],
                'espece' => $row['Espèce'],
                'origine' => $row['Origine'],
                'date_emballage' => $emballageDate,
                'date_consommation' => $consommationDate,
                'imp' => $imp,
                'hx' => $hx,
                'note_nucleotide' => $row['Note Nucléotide'] ?? null,
                'cotation_fraicheur' => $row['Cotation fraîcheur'] ?? null,
                'observations' => $row['Observations'] ?? null,
                'ref_rapport' => $row['Ref Rapport'] ?? null,
            ];
        }
        
        return $processedData;
    }
    
    /**
     * Parse prelevement data (e.g., "COPROMER, 27/08/2025, 11h30")
     */
    protected function parsePrelevementData(string $data): array
    {
        $result = ['lieu' => null, 'date_heure' => null];
        
        if (preg_match('/^\s*([^,]+),\s*(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2}h\d{0,2})?/i', $data, $matches)) {
            $result['lieu'] = trim($matches[1]);
            $time = isset($matches[3]) ? ' ' . str_replace('h', ':', rtrim($matches[3], 'h') . ':00') : ' 00:00:00';
            $result['date_heure'] = Carbon::createFromFormat('d/m/Y H:i:s', trim($matches[2]) . $time);
        }
        
        return $result;
    }
    
    /**
     * Parse reception data (e.g., "25/08/2025, 11h45, 4°C")
     */
    protected function parseReceptionData(string $data): array
    {
        $result = ['date_heure' => null, 'temperature' => null];
        
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2}h\d{0,2}),?\s*([\d.]+)°C?/i', $data, $matches)) {
            $time = str_replace('h', ':', rtrim($matches[2], 'h') . ':00');
            $result['date_heure'] = Carbon::createFromFormat('d/m/Y H:i:s', trim($matches[1]) . ' ' . $time);
            $result['temperature'] = (float)$matches[3];
        }
        
        return $result;
    }
    
    /**
     * Parse date time string (e.g., "25/08/2025, 13h")
     */
    protected function parseDateTime(string $dateTime): ?Carbon
    {
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2})h(\d{0,2})?/i', $dateTime, $matches)) {
            $time = $matches[2] . ':' . (!empty($matches[3]) ? $matches[3] : '00') . ':00';
            return Carbon::createFromFormat('d/m/Y H:i:s', $matches[1] . ' ' . $time);
        }
        
        return null;
    }
}


