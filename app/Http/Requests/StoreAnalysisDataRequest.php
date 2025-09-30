<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnalysisDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Or add your authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rows' => 'required|array',
            'rows.*' => 'array',
        ];
    }

    /**
     * Process and validate the incoming data from the spreadsheet
     *
     * @return array
     */
    public function validatedData(): array
    {
        $validated = $this->validated();
        $result = [];

        foreach ($validated['rows'] as $row) {
            if (!is_array($row)) continue;

            // Parse reception data (e.g., "25/08/2025, 11h45, 4°C")
            $reception = $this->parseReceptionData($row['Date, heure et T°C à la réception au laboratoire'] ?? '');
            
            // Parse analysis date (e.g., "25/08/2025, 13h")
            $analysisDate = $this->parseDateTime($row['Date de mise en analyse'] ?? '');
            
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
            
            $result[] = [
                'numero_rapport' => $row['Numéro Rapport'] ?? null,
                'date_heure_prelevement' => $this->parseDateTime($row['Date et lieu de prélèvement'] ?? ''),
                'date_heure_reception_laboratoire' => $reception['date_heure'] ?? null,
                'temperature_reception' => $reception['temperature'] ?? null,
                'conditions_conservation' => $row['Conditions de conservation à la réception'] ?? null,
                'date_heure_analyse' => $analysisDate,
                'fournisseur_fabricant' => $row['Fournisseur/Fabricant'] ?? null,
                'conditionnement' => $row['Conditionnement'] ?? null,
                'agrement' => $row['Agrément'] ?? null,
                'lot' => $row['Lot'] ?? null,
                'type_peche' => $row['Type de pèche'] ?? null,
                'nom_produit' => $row['Nom de produit'] ?? null,
                'espece' => $row['Espèce'] ?? null,
                'origine' => $row['Origine'] ?? null,
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
        
        return $result;
    }
    
    /**
     * Parse reception data string (e.g., "25/08/2025, 11h45, 4°C")
     */
    protected function parseReceptionData(string $input): array
    {
        $result = [
            'date_heure' => null,
            'temperature' => null
        ];
        
        // Extract date and time
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2})h(\d{0,2})?/i', $input, $matches)) {
            $time = $matches[2] . ':' . (!empty($matches[3]) ? $matches[3] : '00') . ':00';
            $result['date_heure'] = Carbon::createFromFormat('d/m/Y H:i:s', $matches[1] . ' ' . $time);
        }
        
        // Extract temperature
        if (preg_match('/(\d+(\.\d+)?)\s*°C/i', $input, $tempMatch)) {
            $result['temperature'] = (float)$tempMatch[1];
        }
        
        return $result;
    }
    
    /**
     * Parse date time string (e.g., "25/08/2025, 13h")
     */
    protected function parseDateTime(string $dateTime): ?string
    {
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2})h(\d{0,2})?/i', $dateTime, $matches)) {
            $time = $matches[2] . ':' . (!empty($matches[3]) ? $matches[3] : '00') . ':00';
            return Carbon::createFromFormat('d/m/Y H:i:s', $matches[1] . ' ' . $time)->toDateTimeString();
        }
        
        return null;
    }
}
